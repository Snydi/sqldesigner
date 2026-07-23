<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/** @property string $email */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'email_verified_at',
        'google_id',
        'github_id',
        'gitlab_id',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return HasMany<Diagram, $this> */
    public function diagrams(): HasMany
    {
        return $this->hasMany(Diagram::class);
    }

    /** @return HasMany<DiagramLike, $this> */
    public function diagramLikes(): HasMany
    {
        return $this->hasMany(DiagramLike::class);
    }

    /** @return HasMany<Subscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /** @return HasMany<Payment, $this> */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** @return HasMany<Promocode, $this> */
    public function redeemedPromocodes(): HasMany
    {
        return $this->hasMany(Promocode::class, 'redeemed_by');
    }

    /** @return HasOne<Subscription, $this> */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->providingProAccess()
            ->ofMany('ends_at', 'max');
    }

    public function isPro(): bool
    {
        return $this->activeSubscription()->exists();
    }
}
