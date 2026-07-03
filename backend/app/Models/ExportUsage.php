<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportUsage extends Model
{
    protected $fillable = [
        'user_id',
        'usage_date',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
