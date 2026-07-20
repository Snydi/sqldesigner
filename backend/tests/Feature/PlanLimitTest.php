<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ExportStatus;
use App\Enums\SubscriptionStatus;
use App\Jobs\ExportDiagramJob;
use App\Models\Diagram;
use App\Models\ExportUsage;
use App\Models\FeatureFlag;
use App\Models\Subscription;
use App\Models\User;
use App\Services\DiagramSqlService;
use App\Services\PlanLimitService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PlanLimitTest extends TestCase
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
            'schema' => $this->schema(),
        ]);
        $this->setLimitsEnabled(false);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Cache::forget('feature_flag:'.PlanLimitService::FLAG_KEY);

        parent::tearDown();
    }

    public function test_limits_disabled_leaves_diagrams_and_all_export_types_unrestricted(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->auth()->postJson('/api/diagrams', ['name' => "Additional {$i}"])->assertCreated();
        }

        for ($i = 0; $i < 4; $i++) {
            $this->auth()->getJson("/api/diagrams/json/export/{$this->diagram->id}")->assertOk();
            $this->auth()->get("/api/diagrams/migration/export/{$this->diagram->id}")->assertOk();
            $this->auth()->postJson("/api/diagrams/png/export/{$this->diagram->id}")->assertOk();
            $this->runSqlExport();
            $this->assertSame(ExportStatus::DONE, $this->diagram->fresh()->export_status);
        }

        $this->assertDatabaseMissing('export_usages', ['user_id' => $this->user->id]);
    }

    public function test_free_user_is_limited_to_one_diagram_while_grandfathered_diagrams_remain_available(): void
    {
        $this->setLimitsEnabled(true);

        $this->auth()->postJson('/api/diagrams', ['name' => 'Second diagram'])->assertForbidden();

        Diagram::factory()->count(2)->create(['user_id' => $this->user->id]);

        $this->auth()->getJson("/api/diagrams/{$this->diagram->id}")->assertOk();
        $this->auth()->postJson('/api/diagrams', ['name' => 'Another diagram'])->assertForbidden();
        $this->assertSame(3, $this->user->diagrams()->count());
    }

    public function test_sql_json_migration_and_png_exports_share_one_daily_allowance(): void
    {
        $this->setLimitsEnabled(true);

        $this->auth()->getJson("/api/diagrams/json/export/{$this->diagram->id}")->assertOk();
        $this->auth()->get("/api/diagrams/migration/export/{$this->diagram->id}")->assertOk();
        $this->auth()->postJson("/api/diagrams/png/export/{$this->diagram->id}")->assertOk();
        $this->assertSame(3, $this->exportsUsedToday());

        $this->runSqlExport();
        $this->assertSame(ExportStatus::FAILED, $this->diagram->fresh()->export_status);
        $this->assertSame(3, $this->exportsUsedToday());
    }

    public function test_failed_json_migration_and_sql_exports_do_not_consume_an_allowance(): void
    {
        $this->setLimitsEnabled(true);
        $this->diagram->update(['schema' => null]);

        $this->auth()->getJson("/api/diagrams/json/export/{$this->diagram->id}")->assertServerError();
        $this->auth()->get("/api/diagrams/migration/export/{$this->diagram->id}")->assertServerError();

        try {
            $this->runSqlExport();
            $this->fail('The invalid SQL export should fail.');
        } catch (\Throwable) {
            (new ExportDiagramJob($this->diagram->fresh(), $this->user->id))->failed(new \RuntimeException('Invalid schema'));
        }

        $this->assertSame(0, $this->exportsUsedToday());
    }

    public function test_export_allowance_resets_at_europe_moscow_midnight(): void
    {
        $this->setLimitsEnabled(true);
        $limits = app(PlanLimitService::class);

        Carbon::setTestNow(Carbon::parse('2026-07-20 20:59:59', 'UTC'));
        $this->assertTrue($limits->consumeExportAllowance($this->user));

        Carbon::setTestNow(Carbon::parse('2026-07-20 21:00:00', 'UTC'));
        $this->assertTrue($limits->consumeExportAllowance($this->user));

        $this->assertSame(2, ExportUsage::where('user_id', $this->user->id)->count());
        $this->assertSame(1, $this->exportsUsedToday());
    }

    public function test_pro_bypass_expiration_and_cancelled_access_until_expiry(): void
    {
        $this->setLimitsEnabled(true);
        $limits = app(PlanLimitService::class);

        $active = $this->subscription(SubscriptionStatus::ACTIVE, now()->subDay(), now()->addDay());
        for ($i = 0; $i < 4; $i++) {
            $this->assertTrue($limits->consumeExportAllowance($this->user));
        }
        $this->assertDatabaseMissing('export_usages', ['user_id' => $this->user->id]);

        $active->cancel();
        $this->assertTrue($this->user->fresh()->isPro());
        $this->assertTrue($limits->consumeExportAllowance($this->user->fresh()));

        $active->update(['ends_at' => now()->subSecond()]);
        $this->assertFalse($this->user->fresh()->isPro());
        $this->assertTrue($limits->consumeExportAllowance($this->user->fresh()));
        $this->assertSame(1, $this->exportsUsedToday());
    }

    public function test_plan_limit_and_export_endpoints_require_their_expected_authorization(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);

        $this->getJson('/api/plan-limits')->assertUnauthorized();
        $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/diagrams/json/export/{$this->diagram->id}")
            ->assertForbidden();
        $this->actingAs($otherUser, 'sanctum')
            ->get("/api/diagrams/migration/export/{$this->diagram->id}")
            ->assertForbidden();
        $this->actingAs($otherUser, 'sanctum')
            ->postJson("/api/diagrams/png/export/{$this->diagram->id}")
            ->assertForbidden();
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

    private function exportsUsedToday(): int
    {
        return app(PlanLimitService::class)->exportsUsedToday($this->user->fresh());
    }

    private function runSqlExport(): void
    {
        (new ExportDiagramJob($this->diagram->fresh(), $this->user->id))
            ->handle(app(DiagramSqlService::class), app(PlanLimitService::class));
    }

    private function subscription(SubscriptionStatus $status, Carbon $startsAt, Carbon $endsAt): Subscription
    {
        return Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => $status,
            'provider' => 'robokassa',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function schema(): array
    {
        return [
            ['id' => 't1', 'type' => 'table', 'label' => 'users', 'data' => ['uniqueTogether' => [], 'fulltextIndexes' => []]],
            ['id' => 'r1', 'type' => 'row', 'label' => 'id', 'parentNode' => 't1', 'data' => ['sqlType' => 'INT', 'nullable' => false, 'unsigned' => false, 'keyMod' => 'PRIMARY KEY', 'defaultValue' => null, 'comment' => null]],
        ];
    }
}
