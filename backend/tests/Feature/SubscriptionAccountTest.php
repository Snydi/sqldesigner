<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SubscriptionAccountTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_subscription_endpoint_returns_current_access_and_safe_payment_history(): void
    {
        $subscription = $this->activeSubscription();
        Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'provider' => 'robokassa',
            'provider_invoice_id' => '987',
            'status' => PaymentStatus::SUCCEEDED,
            'amount_minor' => 1000,
            'currency' => 'USD',
            'provider_amount_minor' => 100000,
            'provider_currency' => 'RUB',
            'raw_payload' => ['SignatureValue' => 'private-audit-data'],
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/subscription/me')
            ->assertOk()
            ->assertJsonPath('is_pro', true)
            ->assertJsonPath('can_purchase', false)
            ->assertJsonPath('can_cancel', true)
            ->assertJsonPath('subscription.status', 'active')
            ->assertJsonPath('payments.0.amount', '10.00')
            ->assertJsonMissingPath('payments.0.raw_payload');

        $this->assertSame('USD', $response->json('payments.0.currency'));
    }

    public function test_cancellation_keeps_pro_until_period_end_and_is_idempotent(): void
    {
        $subscription = $this->activeSubscription();
        $endsAt = $subscription->ends_at->copy();

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/subscription/current')
            ->assertOk();
        $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/subscription/current')
            ->assertOk();

        $subscription->refresh();
        $this->assertSame(SubscriptionStatus::CANCELLED, $subscription->status);
        $this->assertTrue($subscription->ends_at->equalTo($endsAt));
        $this->assertTrue($this->user->isPro());

        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/subscription/me')
            ->assertJsonPath('can_cancel', false)
            ->assertJsonPath('subscription.provides_access', true);
    }

    public function test_free_user_cannot_cancel_and_can_purchase(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/subscription/me')
            ->assertOk()
            ->assertJsonPath('is_pro', false)
            ->assertJsonPath('can_purchase', true)
            ->assertJsonPath('subscription', null);

        $this->actingAs($this->user, 'sanctum')
            ->deleteJson('/api/subscription/current')
            ->assertStatus(409);
    }

    public function test_subscription_endpoints_require_authentication(): void
    {
        $this->getJson('/api/subscription/me')->assertUnauthorized();
        $this->deleteJson('/api/subscription/current')->assertUnauthorized();
    }

    private function activeSubscription(): Subscription
    {
        return Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);
    }
}
