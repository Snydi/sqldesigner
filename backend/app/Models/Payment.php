<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property PaymentStatus $status
 * @property array<string, mixed>|null $raw_payload
 */
class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'provider',
        'provider_invoice_id',
        'provider_payment_id',
        'status',
        'amount_minor',
        'currency',
        'fee_minor',
        'payer_email',
        'payment_method',
        'paid_currency_label',
        'raw_payload',
        'paid_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount_minor' => 'integer',
            'fee_minor' => 'integer',
            'raw_payload' => 'array',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Subscription, $this> */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
