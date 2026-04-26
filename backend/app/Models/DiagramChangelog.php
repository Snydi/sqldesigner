<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagramChangelog extends Model
{
    const UPDATED_AT = null;

    protected $table = 'diagram_changelog';

    protected $fillable = ['diagram_id', 'user_id', 'user_name', 'action', 'details'];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime',
    ];

    public function diagram(): BelongsTo
    {
        return $this->belongsTo(Diagram::class);
    }
}
