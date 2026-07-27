<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\FeatureFlag;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionConsent;
use App\Models\User;
use App\Services\RobokassaService;
use App\Services\SubscriptionPaymentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RobokassaPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private User $user;
    private \OpenSSLAsymmetricKey $jwsPrivateKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $jwsPrivateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        $this->assertInstanceOf(\OpenSSLAsymmetricKey::class, $jwsPrivateKey);
        $this->jwsPrivateKey = $jwsPrivateKey;
        $jwsKeyDetails = openssl_pkey_get_details($this->jwsPrivateKey);
        $this->assertIsArray($jwsKeyDetails);

        config([
            'robokassa.merchant_login' => 'demo',
            'robokassa.password1' => 'password_1',
            'robokassa.password2' => 'password_2',
            'robokassa.hash_algorithm' => 'md5',
            'robokassa.payment_url' => 'https://auth.robokassa.ru/Merchant/Index.aspx',
            'robokassa.test_mode' => true,
            'robokassa.provider_amount' => '1000.00',
            'robokassa.provider_currency' => 'RUB',
            'robokassa.culture' => 'en',
            'robokassa.payment_method' => null,
            'robokassa.checkout_expires_minutes' => 30,
            'robokassa.renew_before_hours' => 24,
            'robokassa.receipt.enabled' => true,
            'robokassa.receipt.item_name' => 'SQL Designer Pro - monthly subscription',
            'robokassa.receipt.payment_method' => 'full_payment',
            'robokassa.receipt.payment_object' => 'service',
            'robokassa.receipt.tax' => 'none',
            'robokassa.receipt.sno' => null,
            'robokassa.result_url' => 'https://sql-designer.test/api/webhooks/robokassa/result',
            'robokassa.jws_public_key' => $jwsKeyDetails['key'],
            'robokassa.success_url' => 'https://sql-designer.test/checkout/success',
            'robokassa.fail_url' => 'https://sql-designer.test/checkout/fail',
        ]);
        FeatureFlag::updateOrCreate(['key' => 'plan_limits_enabled'], ['enabled' => true]);
        cache()->forget('feature_flag:plan_limits_enabled');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_checkout_creates_pending_records_and_signed_robokassa_form(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated()
            ->assertJsonPath('form.action', 'https://auth.robokassa.ru/Merchant/Index.aspx')
            ->assertJsonPath('form.method', 'POST')
            ->assertJsonPath('form.fields.MerchantLogin', 'demo')
            ->assertJsonPath('form.fields.OutSum', '1000.00')
            ->assertJsonPath('form.fields.Shp_currency', 'USD')
            ->assertJsonPath('form.fields.Recurring', 'true')
            ->assertJsonPath('form.fields.IsTest', '1');

        $payment = Payment::findOrFail($response->json('payment_id'));
        $subscription = $payment->subscription;

        $this->assertSame((string) $payment->id, $payment->provider_invoice_id);
        $this->assertSame(PaymentStatus::INITIATED, $payment->status);
        $this->assertSame(1000, $payment->amount_minor);
        $this->assertSame('USD', $payment->currency);
        $this->assertSame(100000, $payment->provider_amount_minor);
        $this->assertSame('RUB', $payment->provider_currency);
        $this->assertSame(SubscriptionStatus::PENDING, $subscription->status);
        $consent = SubscriptionConsent::query()->where('payment_id', $payment->id)->sole();
        $this->assertSame(SubscriptionConsent::CONSENT_TEXT, $consent->consent_text);
        $this->assertSame(SubscriptionConsent::OFFER_VERSION, $consent->document_version);
        $this->assertNotNull($consent->accepted_at);
        $this->assertNotNull($payment->expires_at);
        $this->assertStringContainsString('SignatureValue=', $response->json('payment_url'));
        $this->assertNotEmpty($response->json('form.fields.Receipt'));

        $fields = $response->json('form.fields');
        $expectedSignature = md5(implode(':', [
            'demo',
            '1000.00',
            (string) $payment->id,
            $fields['Receipt'],
            $fields['ResultUrl2'],
            $fields['SuccessUrl2'],
            $fields['SuccessUrl2Method'],
            $fields['FailUrl2'],
            $fields['FailUrl2Method'],
            'password_1',
            'Shp_currency=USD',
            'Shp_payment_id='.$payment->id,
            'Shp_user_id='.$this->user->id,
        ]));
        $this->assertSame($expectedSignature, $fields['SignatureValue']);
        $this->assertSame('https://sql-designer.test/api/webhooks/robokassa/result', $fields['ResultUrl2']);
        $this->assertSame('https://sql-designer.test/checkout/success', $fields['SuccessUrl2']);
        $this->assertSame('https://sql-designer.test/checkout/fail', $fields['FailUrl2']);

        $receipt = json_decode(urldecode($fields['Receipt']), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('SQL Designer Pro - monthly subscription', $receipt['items'][0]['name']);
        $this->assertSame(1, $receipt['items'][0]['quantity']);
        $this->assertSame(1000, $receipt['items'][0]['sum']);
        $this->assertSame('service', $receipt['items'][0]['payment_object']);
        $this->assertSame('full_payment', $receipt['items'][0]['payment_method']);
        $this->assertSame('none', $receipt['items'][0]['tax']);
    }

    public function test_result_callback_activates_pro_and_records_audit_fields(): void
    {
        $payment = $this->createCheckoutPayment();
        $payload = $this->resultPayload($payment, [
            'Fee' => '0.000000',
            'EMail' => $this->user->email,
            'PaymentMethod' => 'BankCard',
            'IncCurrLabel' => 'BankCardPSR',
        ]);

        $this->post('/api/webhooks/robokassa/result', $payload)
            ->assertOk()
            ->assertSeeText('OK'.$payment->provider_invoice_id);

        $payment->refresh();
        $subscription = $payment->subscription;
        $this->assertSame(PaymentStatus::SUCCEEDED, $payment->status);
        $this->assertSame(0, $payment->fee_minor);
        $this->assertSame('BankCard', $payment->payment_method);
        $this->assertSame('BankCardPSR', $payment->paid_currency_label);
        $this->assertSame($payload['OutSum'], $payment->raw_payload['OutSum']);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(SubscriptionStatus::ACTIVE, $subscription->status);
        $this->assertTrue($this->user->isPro());
        $this->assertDatabaseHas('payment_webhook_logs', [
            'payment_id' => $payment->id,
            'status' => 'processed',
            'http_status' => 200,
        ]);
    }

    public function test_result_url_2_jws_activates_pro_and_records_provider_operation(): void
    {
        $payment = $this->createCheckoutPayment();
        $jws = $this->resultUrl2Jws($payment);

        $this->call(
            'POST',
            '/api/webhooks/robokassa/result',
            server: ['CONTENT_TYPE' => 'application/jose'],
            content: $jws,
        )
            ->assertOk()
            ->assertSeeText('OK'.$payment->provider_invoice_id);

        $payment->refresh();
        $this->assertSame(PaymentStatus::SUCCEEDED, $payment->status);
        $this->assertSame('operation-'.$payment->id, $payment->provider_payment_id);
        $this->assertSame('BankCard', $payment->payment_method);
        $this->assertSame('robokassa_result_url_2', $payment->raw_payload['source']);
        $this->assertSame(SubscriptionStatus::ACTIVE, $payment->subscription->status);
        $this->assertTrue($this->user->isPro());
        $this->assertDatabaseHas('payment_webhook_logs', [
            'payment_id' => $payment->id,
            'status' => 'processed',
            'http_status' => 200,
        ]);
    }

    public function test_result_url_2_rejects_an_invalid_jws_signature(): void
    {
        $payment = $this->createCheckoutPayment();
        $jws = $this->resultUrl2Jws($payment);
        $parts = explode('.', $jws);
        $parts[2] = $this->base64UrlEncode(str_repeat("\0", 256));
        $jws = implode('.', $parts);

        $this->call(
            'POST',
            '/api/webhooks/robokassa/result',
            server: ['CONTENT_TYPE' => 'application/jose'],
            content: $jws,
        )->assertStatus(422);

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
        $this->assertFalse($this->user->isPro());
    }

    public function test_result_url_2_rejects_a_signed_notification_with_wrong_amount(): void
    {
        $payment = $this->createCheckoutPayment();
        $jws = $this->resultUrl2Jws($payment, ['incSum' => '999.00']);

        $this->call(
            'POST',
            '/api/webhooks/robokassa/result',
            server: ['CONTENT_TYPE' => 'application/jose'],
            content: $jws,
        )->assertStatus(422);

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
    }

    public function test_retried_result_callback_is_idempotent(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-19 12:00:00 UTC'));
        $payment = $this->createCheckoutPayment();
        $payload = $this->resultPayload($payment);

        $this->post('/api/webhooks/robokassa/result', $payload)->assertOk();
        $endsAt = $payment->subscription()->firstOrFail()->ends_at->copy();

        Carbon::setTestNow(now()->addDay());
        $this->post('/api/webhooks/robokassa/result', $payload)->assertOk();

        $this->assertTrue($payment->subscription()->firstOrFail()->ends_at->equalTo($endsAt));
    }

    public function test_invalid_signature_does_not_activate_payment(): void
    {
        $payment = $this->createCheckoutPayment();
        $payload = $this->resultPayload($payment);
        $payload['SignatureValue'] = 'invalid';

        $this->post('/api/webhooks/robokassa/result', $payload)->assertStatus(422);

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
        $this->assertFalse($this->user->isPro());
        $this->assertDatabaseHas('payment_webhook_logs', [
            'payment_id' => $payment->id,
            'status' => 'rejected',
            'http_status' => 422,
        ]);
    }

    public function test_signed_callback_with_wrong_amount_is_rejected(): void
    {
        $payment = $this->createCheckoutPayment();
        $payload = $this->resultPayload($payment, ['OutSum' => '999.00']);

        $this->post('/api/webhooks/robokassa/result', $payload)->assertStatus(422);

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
    }

    public function test_signed_callback_cannot_activate_another_users_payment(): void
    {
        $payment = $this->createCheckoutPayment();
        $payload = $this->resultPayload($payment, ['Shp_user_id' => (string) ($this->user->id + 1)]);

        $this->post('/api/webhooks/robokassa/result', $payload)->assertStatus(422);

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
        $this->assertFalse($this->user->isPro());
    }

    public function test_success_redirect_does_not_activate_payment(): void
    {
        $payment = $this->createCheckoutPayment();

        $this->get('/checkout/success')->assertRedirect('/billing?payment=processing');
        $this->get('/checkout/fail')->assertRedirect('/billing?payment=failed');

        $this->assertSame(PaymentStatus::INITIATED, $payment->fresh()->status);
        $this->assertFalse($this->user->isPro());
    }

    public function test_checkout_requires_authentication_and_complete_configuration(): void
    {
        $this->postJson('/api/subscription/checkout')->assertUnauthorized();

        config(['robokassa.provider_amount' => null]);
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertStatus(503);

        $this->assertSame(0, $this->user->payments()->count());
        $this->assertSame(0, $this->user->subscriptions()->count());
    }

    public function test_checkout_requires_explicit_recurring_payment_consent(): void
    {
        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => false])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recurring_payment_consent');

        $this->assertSame(0, $this->user->payments()->count());
        $this->assertSame(0, SubscriptionConsent::query()->where('user_id', $this->user->id)->count());
    }

    public function test_checkout_remains_available_while_plan_limits_are_disabled(): void
    {
        FeatureFlag::where('key', 'plan_limits_enabled')->update(['enabled' => false]);
        cache()->forget('feature_flag:plan_limits_enabled');

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated()
            ->assertJsonPath('form.action', 'https://auth.robokassa.ru/Merchant/Index.aspx');

        $this->assertSame(1, $this->user->payments()->count());
    }

    public function test_current_pro_user_cannot_buy_an_overlapping_period(): void
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertStatus(409);

        $this->assertSame(0, $this->user->payments()->count());
    }

    public function test_checkout_rejects_invalid_fiscal_receipt_configuration(): void
    {
        config(['robokassa.receipt.tax' => 'invalid']);

        $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertStatus(503);

        $this->assertSame(0, $this->user->payments()->count());
    }

    public function test_blank_fiscal_environment_values_use_safe_defaults(): void
    {
        config([
            'robokassa.receipt.item_name' => '',
            'robokassa.receipt.payment_method' => '',
            'robokassa.receipt.payment_object' => '',
            'robokassa.receipt.tax' => '',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated();

        $receipt = json_decode(
            urldecode($response->json('form.fields.Receipt')),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        $this->assertSame('SQL Designer Pro - monthly subscription', $receipt['items'][0]['name']);
        $this->assertSame('full_payment', $receipt['items'][0]['payment_method']);
        $this->assertSame('service', $receipt['items'][0]['payment_object']);
        $this->assertSame('none', $receipt['items'][0]['tax']);
    }

    public function test_repeated_checkout_reuses_unexpired_pending_payment(): void
    {
        $first = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated()
            ->json('payment_id');
        $second = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated()
            ->json('payment_id');

        $this->assertSame($first, $second);
        $this->assertSame(1, $this->user->payments()->count());
        $this->assertSame(1, $this->user->subscriptions()->count());
        $this->assertSame(2, SubscriptionConsent::query()->where('payment_id', $first)->count());
    }

    public function test_result_signature_matches_robokassa_parameter_order(): void
    {
        $signature = app(RobokassaService::class)->resultSignature('1000.000000', '42', [
            'Shp_user_id' => '7',
            'Shp_payment_id' => '42',
            'Shp_currency' => 'USD',
        ]);

        $this->assertSame('b8d0c9b46adb8d5f51cfaf9b7bab76ee', $signature);
    }

    public function test_due_subscription_is_renewed_after_robokassa_confirms_the_recurring_charge(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-19 12:00:00 UTC'));
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'provider_subscription_id' => '100',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addHours(12),
        ]);
        $endsAt = $subscription->ends_at->copy();
        Http::fake(['https://auth.robokassa.ru/Merchant/Recurring' => Http::response('OK200', 200)]);

        $this->assertSame(1, app(SubscriptionPaymentService::class)->renewDueSubscriptions());
        $payment = $subscription->payments()->latest()->firstOrFail();
        $this->assertSame(PaymentStatus::INITIATED, $payment->status);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://auth.robokassa.ru/Merchant/Recurring'
            && $request['PreviousInvoiceID'] === '100'
            && $request['InvId'] === $payment->provider_invoice_id
            && $request['Email'] === $this->user->email
            && json_decode(urldecode($request['Receipt']), true, flags: JSON_THROW_ON_ERROR)['items'][0]['sum'] === 1000
            && ! isset($request['Recurring']));

        $this->post('/api/webhooks/robokassa/result', $this->resultPayload($payment))->assertOk();

        $this->assertTrue($subscription->fresh()->ends_at->equalTo($endsAt->addMonthNoOverflow()));
    }

    public function test_a_provider_confirmed_failed_renewal_is_retried(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-19 12:00:00 UTC'));
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'provider_subscription_id' => '100',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subMonth(),
            'ends_at' => now(),
        ]);
        $stalePayment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'provider' => 'robokassa',
            'provider_invoice_id' => '101',
            'status' => PaymentStatus::INITIATED,
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'provider_amount_minor' => 100000,
            'provider_currency' => 'RUB',
        ]);
        $stalePayment->forceFill([
            'created_at' => now()->subMinutes(16),
            'updated_at' => now()->subMinutes(16),
        ])->save();
        Http::fake([
            'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpStateExt*' => Http::response('<OperationStateResponse><Result><Code>0</Code></Result><State><Code>10</Code></State></OperationStateResponse>', 200),
            'https://auth.robokassa.ru/Merchant/Recurring' => Http::response('OK102', 200),
        ]);

        $this->assertSame(1, app(SubscriptionPaymentService::class)->renewDueSubscriptions());
        $this->assertSame(PaymentStatus::FAILED, $stalePayment->fresh()->status);
        $this->assertSame(2, $subscription->payments()->count());
    }

    public function test_a_successful_renewal_is_recovered_from_operation_state(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-07-19 12:00:00 UTC'));
        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'provider_subscription_id' => '200',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addHours(12),
        ]);
        $originalEnd = $subscription->ends_at->copy();
        $payment = Payment::create([
            'user_id' => $this->user->id,
            'subscription_id' => $subscription->id,
            'provider' => 'robokassa',
            'provider_invoice_id' => '201',
            'status' => PaymentStatus::INITIATED,
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'provider_amount_minor' => 100000,
            'provider_currency' => 'RUB',
        ]);
        $payment->forceFill([
            'created_at' => now()->subMinutes(16),
            'updated_at' => now()->subMinutes(16),
        ])->save();
        Http::fake([
            'https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpStateExt*' => Http::response('<OperationStateResponse><Result><Code>0</Code></Result><State><Code>100</Code></State></OperationStateResponse>', 200),
        ]);

        $this->assertSame(0, app(SubscriptionPaymentService::class)->renewDueSubscriptions());

        $this->assertSame(PaymentStatus::SUCCEEDED, $payment->fresh()->status);
        $this->assertSame('robokassa_operation_state', $payment->fresh()->raw_payload['source']);
        $this->assertTrue($subscription->fresh()->ends_at->equalTo($originalEnd->addMonthNoOverflow()));
        Http::assertSentCount(1);
    }

    public function test_subscription_outside_renewal_window_is_not_charged(): void
    {
        Subscription::create([
            'user_id' => $this->user->id,
            'plan' => Subscription::PLAN_PRO_MONTHLY,
            'status' => SubscriptionStatus::ACTIVE,
            'provider' => 'robokassa',
            'provider_subscription_id' => '300',
            'amount_minor' => Subscription::PRO_PRICE_MINOR,
            'currency' => Subscription::PRO_CURRENCY,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addHours(25),
        ]);
        Http::fake();

        $this->assertSame(0, app(SubscriptionPaymentService::class)->renewDueSubscriptions());
        Http::assertNothingSent();
    }

    private function createCheckoutPayment(): Payment
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/subscription/checkout', ['recurring_payment_consent' => true])
            ->assertCreated();

        return Payment::findOrFail($response->json('payment_id'));
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function resultPayload(Payment $payment, array $overrides = []): array
    {
        $payload = array_merge([
            'OutSum' => '1000.000000',
            'InvId' => (string) $payment->provider_invoice_id,
            'Shp_currency' => $payment->currency,
            'Shp_payment_id' => (string) $payment->id,
            'Shp_user_id' => (string) $payment->user_id,
        ], $overrides);
        $payload['SignatureValue'] = app(RobokassaService::class)->resultSignature(
            $payload['OutSum'],
            $payload['InvId'],
            array_filter($payload, fn (string $key): bool => str_starts_with($key, 'Shp_'), ARRAY_FILTER_USE_KEY),
        );

        return $payload;
    }

    /** @param array<string, string> $overrides */
    private function resultUrl2Jws(Payment $payment, array $overrides = []): string
    {
        $header = $this->base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => 'RS256',
        ], JSON_THROW_ON_ERROR));
        $payload = $this->base64UrlEncode(json_encode([
            'header' => [
                'type' => 'PaymentStateNotification',
                'version' => '1.0.0',
                'timestamp' => (string) now()->timestamp,
            ],
            'data' => array_merge([
                'shop' => 'demo',
                'opKey' => 'operation-'.$payment->id,
                'invId' => (string) $payment->provider_invoice_id,
                'paymentMethod' => 'BankCard',
                'incSum' => '1000.00',
                'state' => 'OK',
            ], $overrides),
        ], JSON_THROW_ON_ERROR));
        $signingInput = $header.'.'.$payload;
        $signed = openssl_sign($signingInput, $signature, $this->jwsPrivateKey, OPENSSL_ALGO_SHA256);
        $this->assertTrue($signed);

        return $signingInput.'.'.$this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
