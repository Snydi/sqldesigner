<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

class SubscriptionPaymentService
{
    public function __construct(private readonly RobokassaService $robokassa) {}

    /** @return array{payment: Payment, parameters: array<string, string>, payment_url: string} */
    public function createCheckout(User $user): array
    {
        $this->robokassa->assertConfigured();

        $payment = DB::transaction(function () use ($user): Payment {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($lockedUser->isPro()) {
                throw new LogicException('Pro access is already active.');
            }

            $existingPayment = $lockedUser->payments()
                ->where('provider', 'robokassa')
                ->where('status', PaymentStatus::INITIATED->value)
                ->where('expires_at', '>', now())
                ->latest()
                ->first();
            if ($existingPayment !== null) {
                return $existingPayment->load('user');
            }

            $subscription = Subscription::create([
                'user_id' => $lockedUser->id,
                'plan' => Subscription::PLAN_PRO_MONTHLY,
                'status' => SubscriptionStatus::PENDING,
                'provider' => 'robokassa',
                'amount_minor' => Subscription::PRO_PRICE_MINOR,
                'currency' => Subscription::PRO_CURRENCY,
            ]);

            $payment = Payment::create([
                'user_id' => $lockedUser->id,
                'subscription_id' => $subscription->id,
                'provider' => 'robokassa',
                'status' => PaymentStatus::INITIATED,
                'amount_minor' => Subscription::PRO_PRICE_MINOR,
                'currency' => Subscription::PRO_CURRENCY,
                'provider_amount_minor' => $this->robokassa->providerAmountMinor(),
                'provider_currency' => (string) config('robokassa.provider_currency', 'RUB'),
                'expires_at' => now()->addMinutes((int) config('robokassa.checkout_expires_minutes', 30)),
            ]);
            $payment->forceFill(['provider_invoice_id' => (string) $payment->id])->save();

            return $payment->load('user');
        });

        $parameters = $this->robokassa->checkoutParameters($payment);

        return [
            'payment' => $payment,
            'parameters' => $parameters,
            'payment_url' => $this->robokassa->paymentUrlFromParameters($parameters),
        ];
    }

    /** @param array<string, mixed> $payload */
    public function processSuccessfulPayment(array $payload): string
    {
        $this->robokassa->assertConfigured();

        if (! $this->robokassa->verifyResultSignature($payload)) {
            throw new InvalidArgumentException('Invalid Robokassa signature.');
        }

        $invoiceId = (string) ($payload['InvId'] ?? '');

        DB::transaction(function () use ($payload, $invoiceId): void {
            $payment = Payment::query()
                ->where('provider', 'robokassa')
                ->where('provider_invoice_id', $invoiceId)
                ->lockForUpdate()
                ->first();
            if ($payment === null) {
                throw (new ModelNotFoundException)->setModel(Payment::class, [$invoiceId]);
            }

            $this->validateCallbackOwnership($payment, $payload);

            $this->completeSuccessfulPayment($payment, [
                'fee_minor' => filled($payload['Fee'] ?? null)
                    ? $this->robokassa->parseMinorAmount($this->scalarString($payload['Fee']), true)
                    : null,
                'payer_email' => $this->nullableScalarString($payload['EMail'] ?? null),
                'payment_method' => $this->nullableScalarString($payload['PaymentMethod'] ?? null),
                'paid_currency_label' => $this->nullableScalarString($payload['IncCurrLabel'] ?? null),
                'raw_payload' => $payload,
            ]);
        });

        return $invoiceId;
    }

    public function renewDueSubscriptions(): int
    {
        $renewBefore = now()->addHours(max(1, (int) config('robokassa.renew_before_hours', 24)));
        $subscriptions = Subscription::query()
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('provider', 'robokassa')
            ->whereNotNull('provider_subscription_id')
            ->where('ends_at', '<=', $renewBefore)
            ->get();

        $initiated = 0;
        foreach ($subscriptions as $subscription) {
            if ($this->initiateRenewal($subscription, $renewBefore)) {
                $initiated++;
            }
        }

        return $initiated;
    }

    private function initiateRenewal(Subscription $subscription, Carbon $renewBefore): bool
    {
        $this->reconcilePendingRenewals($subscription);

        $payment = DB::transaction(function () use ($subscription, $renewBefore): ?Payment {
            $subscription = Subscription::query()->lockForUpdate()->findOrFail($subscription->id);
            if ($subscription->status !== SubscriptionStatus::ACTIVE
                || $subscription->provider_subscription_id === null
                || $subscription->ends_at === null
                || $subscription->ends_at->greaterThan($renewBefore)) {
                return null;
            }

            if ($subscription->payments()->where('status', PaymentStatus::INITIATED->value)->exists()) {
                return null;
            }

            $payment = Payment::create([
                'user_id' => $subscription->user_id,
                'subscription_id' => $subscription->id,
                'provider' => 'robokassa',
                'status' => PaymentStatus::INITIATED,
                'amount_minor' => Subscription::PRO_PRICE_MINOR,
                'currency' => Subscription::PRO_CURRENCY,
                'provider_amount_minor' => $this->robokassa->providerAmountMinor(),
                'provider_currency' => (string) config('robokassa.provider_currency', 'RUB'),
            ]);
            $payment->forceFill(['provider_invoice_id' => (string) $payment->id])->save();

            return $payment->load('user', 'subscription');
        });

        if ($payment === null) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(15)
                ->post((string) config('robokassa.recurring_payment_url'), $this->robokassa->recurringPaymentParameters(
                    $payment,
                    (string) $payment->subscription->provider_subscription_id,
                ));
            if (! $response->successful() || ! str_starts_with(trim($response->body()), 'OK')) {
                throw new RuntimeException('Robokassa did not accept the recurring payment.');
            }
        } catch (Throwable $exception) {
            report($exception);
            $payment->forceFill(['status' => PaymentStatus::FAILED, 'failed_at' => now()])->save();

            return false;
        }

        return true;
    }

    private function reconcilePendingRenewals(Subscription $subscription): void
    {
        $checkAfter = now()->subMinutes((int) config('robokassa.operation_state_check_after_minutes', 15));
        $payments = $subscription->payments()
            ->where('status', PaymentStatus::INITIATED->value)
            ->where('created_at', '<=', $checkAfter)
            ->get();

        foreach ($payments as $payment) {
            try {
                $state = $this->robokassa->operationState($payment);
            } catch (Throwable $exception) {
                report($exception);

                continue;
            }

            if ($state === 100) {
                DB::transaction(function () use ($payment): void {
                    $lockedPayment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
                    $this->completeSuccessfulPayment($lockedPayment, [
                        'raw_payload' => [
                            'source' => 'robokassa_operation_state',
                            'state' => 100,
                            'reconciled_at' => now()->toIso8601String(),
                        ],
                    ]);
                });

                continue;
            }

            // 10: cancelled/expired, 60: funds returned. Do not retry a payment
            // unless the provider has confirmed it cannot still succeed.
            if (in_array($state, [10, 60], true)) {
                $payment->forceFill([
                    'status' => PaymentStatus::FAILED,
                    'failed_at' => now(),
                ])->save();
            }
        }
    }

    /** @param array<string, mixed> $auditFields */
    private function completeSuccessfulPayment(Payment $payment, array $auditFields): void
    {
        if ($payment->status === PaymentStatus::SUCCEEDED) {
            return;
        }
        if ($payment->status !== PaymentStatus::INITIATED) {
            throw new LogicException('Payment is not awaiting confirmation.');
        }

        $subscription = Subscription::query()->lockForUpdate()->findOrFail($payment->subscription_id);
        if ($subscription->user_id !== $payment->user_id) {
            throw new InvalidArgumentException('Payment subscription ownership does not match.');
        }

        $payment->forceFill([
            ...$auditFields,
            'status' => PaymentStatus::SUCCEEDED,
            'paid_at' => now(),
            'failed_at' => null,
        ])->save();
        $subscription->activate();
        if ($subscription->provider_subscription_id === null) {
            $subscription->forceFill(['provider_subscription_id' => $payment->provider_invoice_id])->save();
        } elseif ($payment->provider_invoice_id !== $subscription->provider_subscription_id) {
            $subscription->renew();
        }
    }

    public function cancelCurrentSubscription(User $user): Subscription
    {
        return DB::transaction(function () use ($user): Subscription {
            User::query()->lockForUpdate()->findOrFail($user->id);
            $subscription = Subscription::query()
                ->where('user_id', $user->id)
                ->where('provider', 'robokassa')
                ->providingProAccess()
                ->orderByDesc('ends_at')
                ->lockForUpdate()
                ->first();
            if ($subscription === null) {
                throw new LogicException('There is no active Pro subscription to cancel.');
            }

            $subscription->cancel();

            $subscription->payments()
                ->where('status', PaymentStatus::INITIATED->value)
                ->update([
                    'status' => PaymentStatus::FAILED->value,
                    'failed_at' => now(),
                    'updated_at' => now(),
                ]);

            return $subscription->refresh();
        });
    }

    /** @param array<string, mixed> $payload */
    private function validateCallbackOwnership(Payment $payment, array $payload): void
    {
        if ($this->scalarString($payload['Shp_payment_id'] ?? null) !== (string) $payment->id
            || $this->scalarString($payload['Shp_user_id'] ?? null) !== (string) $payment->user_id) {
            throw new InvalidArgumentException('Robokassa payment ownership does not match.');
        }
        if ($this->scalarString($payload['Shp_currency'] ?? null) !== $payment->currency) {
            throw new InvalidArgumentException('Robokassa product currency does not match.');
        }
        if ($this->robokassa->parseMinorAmount($this->scalarString($payload['OutSum'] ?? null)) !== $payment->provider_amount_minor) {
            throw new InvalidArgumentException('Robokassa payment amount does not match.');
        }
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    private function nullableScalarString(mixed $value): ?string
    {
        return is_scalar($value) ? (string) $value : null;
    }
}
