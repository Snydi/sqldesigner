<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Http\Requests\Subscription\CheckoutSubscriptionRequest;
use App\Http\Requests\Subscription\RedeemPromocodeRequest;
use App\Models\Payment;
use App\Models\User;
use App\Services\PromocodeService;
use App\Services\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LogicException;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionPaymentService $payments,
        private readonly PromocodeService $promocodes,
    ) {}

    public function checkout(CheckoutSubscriptionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $checkout = $this->payments->createCheckout($user);
        } catch (LogicException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json(['message' => 'Payments are not configured yet.'], 503);
        }

        return $this->created([
            'payment_id' => $checkout['payment']->id,
            'payment_url' => $checkout['payment_url'],
            'form' => [
                'action' => (string) config('robokassa.payment_url'),
                'method' => 'POST',
                'fields' => $checkout['parameters'],
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $current = $user->activeSubscription()->first();
        $latest = $current ?? $user->subscriptions()->latest()->first();
        $cancellable = $user->subscriptions()
            ->where('provider', 'robokassa')
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->where('ends_at', '>', now())
            ->exists();

        return $this->success([
            'is_pro' => $current !== null,
            'can_purchase' => $current === null,
            'can_cancel' => $cancellable,
            'subscription' => $latest === null ? null : [
                'id' => $latest->id,
                'plan' => $latest->plan,
                'status' => $latest->status->value,
                'provides_access' => $latest->providesProAccess(),
                'starts_at' => $latest->starts_at?->toIso8601String(),
                'ends_at' => $latest->ends_at?->toIso8601String(),
                'cancelled_at' => $latest->cancelled_at?->toIso8601String(),
            ],
            'payments' => $user->payments()
                ->latest()
                ->limit(20)
                ->get()
                ->map(fn (Payment $payment): array => [
                    'id' => $payment->id,
                    'status' => $payment->status->value,
                    'amount' => number_format($payment->amount_minor / 100, 2, '.', ''),
                    'currency' => $payment->currency,
                    'provider' => $payment->provider,
                    'created_at' => $payment->created_at?->toIso8601String(),
                    'paid_at' => $payment->paid_at?->toIso8601String(),
                ])
                ->values(),
        ]);
    }

    public function cancel(CancelSubscriptionRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        try {
            $subscription = $this->payments->cancelCurrentSubscription($user);
        } catch (LogicException $exception) {
            return response()->json(['message' => $exception->getMessage()], 409);
        }

        return $this->success([
            'message' => 'Pro has been cancelled. Your access remains active until the current period ends.',
            'ends_at' => $subscription->ends_at?->toIso8601String(),
        ]);
    }

    public function redeem(RedeemPromocodeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        try {
            $subscription = $this->promocodes->redeem($user, $validated['code']);
        } catch (LogicException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return $this->success([
            'message' => 'Promo code applied. Pro access is active until '.$subscription->ends_at?->toIso8601String().'.',
            'ends_at' => $subscription->ends_at?->toIso8601String(),
        ]);
    }

    public function checkoutSuccess(): RedirectResponse
    {
        return redirect('/billing?payment=processing');
    }

    public function checkoutFail(): RedirectResponse
    {
        return redirect('/billing?payment=failed');
    }
}
