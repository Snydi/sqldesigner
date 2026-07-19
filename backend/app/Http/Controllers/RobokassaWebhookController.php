<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SubscriptionPaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

class RobokassaWebhookController extends Controller
{
    public function __construct(private readonly SubscriptionPaymentService $payments) {}

    public function result(Request $request): Response
    {
        foreach (['OutSum', 'InvId', 'SignatureValue', 'Shp_currency', 'Shp_payment_id', 'Shp_user_id'] as $field) {
            if (! $request->filled($field)) {
                return response("Missing Robokassa field: {$field}", 422)->header('Content-Type', 'text/plain');
            }
        }

        try {
            $invoiceId = $this->payments->processSuccessfulPayment($request->all());
        } catch (ModelNotFoundException) {
            return response('Robokassa invoice not found.', 404)->header('Content-Type', 'text/plain');
        } catch (InvalidArgumentException|LogicException $exception) {
            return response($exception->getMessage(), 422)->header('Content-Type', 'text/plain');
        } catch (RuntimeException $exception) {
            report($exception);

            return response('Payment processing is unavailable.', 503)->header('Content-Type', 'text/plain');
        }

        return response('OK'.$invoiceId)->header('Content-Type', 'text/plain');
    }
}
