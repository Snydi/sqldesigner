<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\Promocode;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class PromocodeService
{
    public function generate(int $durationMonths): Promocode
    {
        do {
            $code = Str::upper(Str::random(12));
        } while (Promocode::query()->where('code', $code)->exists());

        return Promocode::create(['code' => $code, 'duration_months' => $durationMonths]);
    }

    public function redeem(User $user, string $code): Subscription
    {
        return DB::transaction(function () use ($user, $code): Subscription {
            $promocode = Promocode::query()
                ->where('code', Str::upper(trim($code)))
                ->lockForUpdate()
                ->first();

            if ($promocode === null) {
                throw new LogicException('This promo code is invalid.');
            }
            if ($promocode->redeemed_at !== null) {
                throw new LogicException('This promo code has already been used.');
            }

            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $latestAccessEnd = Subscription::query()
                ->where('user_id', $lockedUser->id)
                ->providingProAccess()
                ->max('ends_at');
            $startsAt = now();
            $endsAt = $latestAccessEnd !== null && $startsAt->lt($latestAccessEnd)
                ? Carbon::parse($latestAccessEnd)->addMonthsNoOverflow($promocode->duration_months)
                : $startsAt->copy()->addMonthsNoOverflow($promocode->duration_months);

            // Extending an existing Robokassa subscription also postpones its
            // next merchant-initiated charge. Keeping promo time in a parallel
            // subscription would let the paid subscription renew underneath it.
            $subscription = Subscription::query()
                ->where('user_id', $lockedUser->id)
                ->where('provider', 'robokassa')
                ->providingProAccess()
                ->orderByDesc('ends_at')
                ->lockForUpdate()
                ->first();

            if ($subscription !== null) {
                $subscription->forceFill(['ends_at' => $endsAt])->save();
            } else {
                $subscription = Subscription::create([
                    'user_id' => $lockedUser->id,
                    'plan' => Subscription::PLAN_PRO_MONTHLY,
                    'status' => SubscriptionStatus::ACTIVE,
                    'provider' => 'promocode',
                    'provider_subscription_id' => $promocode->code,
                    'amount_minor' => 0,
                    'currency' => Subscription::PRO_CURRENCY,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                ]);
            }

            $promocode->forceFill([
                'redeemed_by' => $lockedUser->id,
                'redeemed_at' => now(),
            ])->save();

            return $subscription;
        });
    }
}
