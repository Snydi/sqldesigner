<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property int $duration_months
 * @property int|null $redeemed_by
 * @property Carbon|null $redeemed_at
 */
class Promocode extends Model
{
    protected $fillable = ['code', 'duration_months'];

    protected function casts(): array
    {
        return [
            'duration_months' => 'integer',
            'redeemed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function redeemedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }
}
