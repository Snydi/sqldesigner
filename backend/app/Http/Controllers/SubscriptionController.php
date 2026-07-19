<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SubscriptionPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LogicException;
use RuntimeException;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionPaymentService $payments) {}

    public function checkout(Request $request): JsonResponse
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

    public function checkoutSuccess(): RedirectResponse
    {
        return redirect('/pricing?payment=processing');
    }

    public function checkoutFail(): RedirectResponse
    {
        return redirect('/pricing?payment=failed');
    }
}
