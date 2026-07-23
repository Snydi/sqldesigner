<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Enums\DiagramAccess;
use App\Enums\VisitorStatus;
use App\Models\Diagram;
use App\Models\DiagramVisitor;
use App\Models\FeatureFlag;
use App\Models\SchemaDoctorUsage;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanLimitService;
use App\Services\SchemaDoctorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SchemaDoctorTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    private Diagram $diagram;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->diagram = Diagram::factory()->create([
            'user_id' => $this->user->id,
            'db_type' => 'mysql',
            'schema' => $this->schema(),
        ]);
        $this->setLimitsEnabled(true);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);

        parent::tearDown();
    }

    public function test_scan_uses_the_persisted_schema_and_returns_allowance(): void
    {
        $response = $this->auth()->postJson(
            "/api/diagrams/{$this->diagram->id}/schema-doctor/scan",
            ['schema' => []]
        );

        $response->assertOk()
            ->assertJsonPath('summary.errors', 0)
            ->assertJsonPath('summary.warnings', 1)
            ->assertJsonPath('diagnostics.0.rule_id', 'table.missing-primary-key')
            ->assertJsonPath('allowance.limit', PlanLimitService::SCHEMA_DOCTOR_DAILY_LIMIT)
            ->assertJsonPath('allowance.used', 1)
            ->assertJsonPath('allowance.remaining', 2);
    }

    public function test_free_user_gets_three_successful_scans_per_day(): void
    {
        for ($scan = 0; $scan < PlanLimitService::SCHEMA_DOCTOR_DAILY_LIMIT; $scan++) {
            $this->auth()
                ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
                ->assertOk();
        }

        $this->auth()
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertForbidden()
            ->assertJsonPath('allowance.remaining', 0);

        $this->assertDatabaseHas('schema_doctor_usages', [
            'user_id' => $this->user->id,
            'count' => PlanLimitService::SCHEMA_DOCTOR_DAILY_LIMIT,
        ]);
    }

    public function test_pro_user_and_disabled_limits_are_unlimited(): void
    {
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        for ($scan = 0; $scan < 4; $scan++) {
            $this->auth()
                ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
                ->assertOk()
                ->assertJsonPath('allowance.limit', null);
        }
        $this->assertDatabaseMissing('schema_doctor_usages', ['user_id' => $this->user->id]);

        $subscription->delete();
        $this->setLimitsEnabled(false);

        $this->auth()
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertOk()
            ->assertJsonPath('allowance.limit', null);
        $this->assertDatabaseMissing('schema_doctor_usages', ['user_id' => $this->user->id]);
    }

    public function test_failed_scan_does_not_consume_an_allowance(): void
    {
        $this->mock(SchemaDoctorService::class)
            ->shouldReceive('scan')
            ->once()
            ->andThrow(new \RuntimeException('Rule engine failed'));

        $this->auth()
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertServerError();

        $this->assertDatabaseMissing('schema_doctor_usages', ['user_id' => $this->user->id]);
    }

    public function test_scan_requires_write_access(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);

        $this->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertUnauthorized();

        $this->actingAs($otherUser, 'sanctum')
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertForbidden();
    }

    public function test_approved_shared_writer_can_scan_but_read_only_visitor_cannot(): void
    {
        $writer = User::factory()->create(['email_verified_at' => now()]);
        $reader = User::factory()->create(['email_verified_at' => now()]);
        $this->diagram->update([
            'share_access' => DiagramAccess::PER_USER,
            'require_approval' => true,
        ]);

        DiagramVisitor::create([
            'diagram_id' => $this->diagram->id,
            'user_id' => $writer->id,
            'status' => VisitorStatus::APPROVED,
            'access' => DiagramAccess::WRITE,
        ]);
        DiagramVisitor::create([
            'diagram_id' => $this->diagram->id,
            'user_id' => $reader->id,
            'status' => VisitorStatus::APPROVED,
            'access' => DiagramAccess::READ,
        ]);

        $this->actingAs($writer, 'sanctum')
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertOk();

        $this->actingAs($reader, 'sanctum')
            ->postJson("/api/diagrams/{$this->diagram->id}/schema-doctor/scan")
            ->assertForbidden();
    }

    public function test_scan_allowance_resets_at_moscow_midnight(): void
    {
        $limits = app(PlanLimitService::class);

        Carbon::setTestNow(Carbon::parse('2026-07-20 20:59:59', 'UTC'));
        $this->assertTrue($limits->consumeSchemaDoctorAllowance($this->user));

        Carbon::setTestNow(Carbon::parse('2026-07-20 21:00:00', 'UTC'));
        $this->assertTrue($limits->consumeSchemaDoctorAllowance($this->user));

        $this->assertSame(2, SchemaDoctorUsage::where('user_id', $this->user->id)->count());
        $this->assertSame(1, $limits->schemaDoctorScansUsedToday($this->user));
    }

    private function auth(): static
    {
        return $this->actingAs($this->user, 'sanctum');
    }

    private function setLimitsEnabled(bool $enabled): void
    {
        FeatureFlag::updateOrCreate(['key' => PlanLimitService::FLAG_KEY], ['enabled' => $enabled]);
        Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);
    }

    /** @return array<int, array<string, mixed>> */
    private function schema(): array
    {
        return [
            ['id' => 't1', 'type' => 'table', 'label' => 'users', 'data' => []],
            ['id' => 'r1', 'type' => 'row', 'label' => 'email', 'parentNode' => 't1', 'data' => [
                'sqlType' => 'VARCHAR(255)',
                'nullable' => false,
                'unsigned' => false,
                'keyMod' => null,
                'defaultValue' => null,
            ]],
        ];
    }
}
