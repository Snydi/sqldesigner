<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    //TODO add this logic as soon as Robokassa activates
    public function isPro(): bool
    {
        return false;
    }
}
