<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentWebhookLog;
use App\Services\SubscriptionPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Throwable;

class RobokassaWebhookController extends Controller
{
    public function __construct(private readonly SubscriptionPaymentService $payments) {}

    public function result(Request $request): Response
    {
        $jws = trim($request->getContent());
        if ($request->all() === [] && $jws !== '') {
            return $this->resultUrl2($request, $jws);
        }

        foreach (['OutSum', 'InvId', 'SignatureValue', 'Shp_currency', 'Shp_payment_id', 'Shp_user_id'] as $field) {
            if (! $request->filled($field)) {
                return $this->resultResponse($request, "Missing Robokassa field: {$field}", 422);
            }
        }

        try {
            $invoiceId = $this->payments->processSuccessfulPayment($request->all());
        } catch (ModelNotFoundException) {
            return $this->resultResponse($request, 'Robokassa invoice not found.', 404);
        } catch (InvalidArgumentException|LogicException $exception) {
            return $this->resultResponse($request, $exception->getMessage(), 422);
        } catch (RuntimeException $exception) {
            report($exception);

            return $this->resultResponse($request, 'Payment processing is unavailable.', 503);
        }

        return $this->resultResponse($request, 'OK'.$invoiceId, 200, 'processed');
    }

    private function resultUrl2(Request $request, string $jws): Response
    {
        try {
            $invoiceId = $this->payments->processResultUrl2Notification($jws);
        } catch (ModelNotFoundException) {
            return $this->resultResponse($request, 'Robokassa invoice not found.', 404);
        } catch (InvalidArgumentException|LogicException $exception) {
            return $this->resultResponse($request, $exception->getMessage(), 422);
        } catch (RuntimeException $exception) {
            report($exception);

            return $this->resultResponse($request, 'Payment processing is unavailable.', 503);
        }

        return $this->resultResponse(
            $request,
            'OK'.$invoiceId,
            200,
            'processed',
            $invoiceId,
            ['format' => 'jws', 'sha256' => hash('sha256', $jws)],
        );
    }

    /** @param array<string, mixed>|null $payload */
    private function resultResponse(
        Request $request,
        string $message,
        int $httpStatus,
        string $status = 'rejected',
        ?string $providerInvoiceId = null,
        ?array $payload = null,
    ): Response
    {
        $invoiceId = $providerInvoiceId ?? $request->input('InvId');
        $invoiceId = is_scalar($invoiceId) ? (string) $invoiceId : null;

        try {
            $paymentId = $invoiceId === null
                ? null
                : Payment::query()
                    ->where('provider', 'robokassa')
                    ->where('provider_invoice_id', $invoiceId)
                    ->value('id');

            PaymentWebhookLog::create([
                'payment_id' => $paymentId,
                'provider' => 'robokassa',
                'provider_invoice_id' => $invoiceId,
                'status' => $status,
                'http_status' => $httpStatus,
                'message' => $message,
                'payload' => $payload ?? $request->all(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }

        return response($message, $httpStatus)->header('Content-Type', 'text/plain');
    }
}
