<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingModelsTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_user_is_not_pro_without_a_current_subscription(): void
    {
        $this->createSubscription(SubscriptionStatus::PENDING);

        $this->assertFalse($this->user->isPro());
        $this->assertNull($this->user->activeSubscription);
    }

    public function test_active_current_subscription_grants_pro_access(): void
    {
        $subscription = $this->createSubscription(SubscriptionStatus::ACTIVE);

        $this->assertTrue($this->user->isPro());
        $this->assertTrue($subscription->providesProAccess());
        $this->assertTrue($this->user->activeSubscription->is($subscription));
    }

    public function test_cancelled_subscription_keeps_access_until_it_expires(): void
    {
        $subscription = $this->createSubscription(SubscriptionStatus::ACTIVE);
        $endsAt = $subscription->ends_at->copy();

        $subscription->cancel();

        $this->assertSame(SubscriptionStatus::CANCELLED, $subscription->status);
        $this->assertNotNull($subscription->cancelled_at);
        $this->assertTrue($subscription->ends_at->equalTo($endsAt));
        $this->assertTrue($this->user->isPro());
    }

    public function test_expired_or_future_subscription_does_not_grant_pro_access(): void
    {
        $this->createSubscription(
            SubscriptionStatus::ACTIVE,
            now()->subMonths(2),
            now()->subMonth()
        );
        $this->createSubscription(
            SubscriptionStatus::ACTIVE,
            now()->addDay(),
            now()->addMonth()
        );

        $this->assertFalse($this->user->isPro());
    }

    public function test_activation_grants_one_calendar_month_without_date_overflow(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-31 12:00:00 UTC'));
        $subscription = $this->createSubscription(SubscriptionStatus::PENDING, null, null);

        $subscription->activate();

        $this->assertSame(SubscriptionStatus::ACTIVE, $subscription->status);
        $this->assertSame('2026-01-31 12:00:00', $subscription->starts_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-02-28 12:00:00', $subscription->ends_at->format('Y-m-d H:i:s'));
    }

    public function test_activation_is_idempotent_for_retried_payment_callbacks(): void
    {
        $subscription = $this->createSubscription(SubscriptionStatus::PENDING, null, null);
        $subscription->activate(Carbon::parse('2026-07-01 12:00:00 UTC'));
        $endsAt = $subscription->ends_at->copy();

        $subscription->activate(Carbon::parse('2026-07-02 12:00:00 UTC'));

        $this->assertTrue($subscription->ends_at->equalTo($endsAt));
    }

    public function test_payment_keeps_robokassa_audit_fields_and_relations(): void
    {
        $subscription = $this->createSubscription(SubscriptionStatus::PENDING);
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'provider' => 'robokassa',
            'provider_invoice_id' => '123456',
            'provider_payment_id' => 'operation-key',
            'status' => PaymentStatus::SUCCEEDED,
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'fee_minor' => 25,
            'payer_email' => $this->user->email,
            'payment_method' => 'BankCard',
            'paid_currency_label' => 'USD',
            'raw_payload' => ['InvId' => '123456', 'OutSum' => '10.000000'],
            'paid_at' => now(),
        ]);

        $payment->refresh();

        $this->assertSame(PaymentStatus::SUCCEEDED, $payment->status);
        $this->assertSame('123456', $payment->raw_payload['InvId']);
        $this->assertTrue($payment->user->is($this->user));
        $this->assertTrue($payment->subscription->is($subscription));
        $this->assertTrue($subscription->payments->contains($payment));
        $this->assertTrue($this->user->payments->contains($payment));
    }

    private function createSubscription(
        SubscriptionStatus $status,
        ?Carbon $startsAt = null,
        ?Carbon $endsAt = null,
    ): Subscription {
        return Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => $status,
            'provider' => 'robokassa',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => $startsAt ?? now()->subDay(),
            'ends_at' => $endsAt ?? now()->addMonth(),
        ]);
    }
}
