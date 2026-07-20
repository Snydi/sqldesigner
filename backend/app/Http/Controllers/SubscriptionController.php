<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\SubscriptionStatus;
use App\Models\Payment;
use App\Models\User;
use App\Services\PlanLimitService;
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
        private readonly PlanLimitService $planLimits,
    ) {}

    public function checkout(Request $request): JsonResponse
    {
        if (! $this->planLimits->limitsEnabled()) {
            return response()->json(['message' => 'Pro payments are not live yet.'], 403);
        }

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

        return $this->success([
            'is_pro' => $current !== null,
            'can_purchase' => $current === null,
            'can_cancel' => $current?->status === SubscriptionStatus::ACTIVE,
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

    public function cancel(Request $request): JsonResponse
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

    public function checkoutSuccess(): RedirectResponse
    {
        return redirect('/billing?payment=processing');
    }

    public function checkoutFail(): RedirectResponse
    {
        return redirect('/billing?payment=failed');
    }
}
