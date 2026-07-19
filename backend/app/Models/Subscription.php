<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use LogicException;

/**
 * @property int $id
 * @property int $user_id
 * @property string $plan
 * @property SubscriptionStatus $status
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $cancelled_at
 */
class Subscription extends Model
{
    public const PLAN_PRO_MONTHLY = 'pro_monthly';

    public const PRO_PRICE_MINOR = 1000;

    public const PRO_CURRENCY = 'USD';

    protected $fillable = [
        'user_id',
        'plan',
        'status',
        'provider',
        'provider_subscription_id',
        'amount_minor',
        'currency',
        'starts_at',
        'ends_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'amount_minor' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeProvidingProAccess(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->where('plan', self::PLAN_PRO_MONTHLY)
            ->whereIn('status', [SubscriptionStatus::ACTIVE->value, SubscriptionStatus::CANCELLED->value])
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>', $at);
    }

    public function providesProAccess(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->plan === self::PLAN_PRO_MONTHLY
            && in_array($this->status, [SubscriptionStatus::ACTIVE, SubscriptionStatus::CANCELLED], true)
            && $this->starts_at !== null
            && $this->starts_at->lessThanOrEqualTo($at)
            && $this->ends_at !== null
            && $this->ends_at->greaterThan($at);
    }

    public function activate(?Carbon $startsAt = null): void
    {
        if (in_array($this->status, [SubscriptionStatus::ACTIVE, SubscriptionStatus::CANCELLED], true)) {
            return;
        }

        if ($this->status !== SubscriptionStatus::PENDING) {
            throw new LogicException('Only a pending subscription can be activated.');
        }

        $startsAt ??= now();

        $this->forceFill([
            'status' => SubscriptionStatus::ACTIVE,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMonthNoOverflow(),
            'cancelled_at' => null,
        ])->save();
    }

    public function cancel(?Carbon $cancelledAt = null): void
    {
        if ($this->status === SubscriptionStatus::CANCELLED) {
            return;
        }

        if ($this->status !== SubscriptionStatus::ACTIVE) {
            throw new LogicException('Only an active subscription can be cancelled.');
        }

        $this->forceFill([
            'status' => SubscriptionStatus::CANCELLED,
            'cancelled_at' => $cancelledAt ?? now(),
        ])->save();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
