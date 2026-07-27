<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $payment_id
 * @property string $consent_type
 * @property string $document_version
 * @property string $document_url
 * @property string $consent_text
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon $accepted_at
 */
class SubscriptionConsent extends Model
{
    public const TYPE_RECURRING_PAYMENT = 'recurring_payment';

    public const OFFER_VERSION = '2026-07-27';

    public const OFFER_URL = '/oferta';

    public const CONSENT_TEXT = 'Я согласен на автоматические списания согласно условиям оферты';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'payment_id',
        'consent_type',
        'document_version',
        'document_url',
        'consent_text',
        'ip_address',
        'user_agent',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Payment, $this> */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
