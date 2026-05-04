<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Diagram extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'db_type',
        'schema',
        'script',
        'user_id',
        'share_token',
        'share_access',
        'require_approval',
        'library',
        'featured',
        'featured_url',
        'import_status',
        'import_error',
    ];

    protected static function booted(): void
    {
        static::creating(function (Diagram $diagram) {
            if (empty($diagram->share_token)) {
                $diagram->share_token = Str::uuid()->toString();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(DiagramVisitor::class);
    }
}
