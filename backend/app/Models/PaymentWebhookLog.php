<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWebhookLog extends Model
{
    protected $fillable = [
        'payment_id',
        'provider',
        'provider_invoice_id',
        'status',
        'http_status',
        'message',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'http_status' => 'integer',
            'payload' => 'array',
        ];
    }

    /** @return BelongsTo<Payment, $this> */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
