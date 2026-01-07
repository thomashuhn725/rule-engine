<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rule extends Model
{
    /** @use HasFactory<\Database\Factories\RuleFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'value_1_type',
        'value_1_id',
        'comparitor_id',
        'value_2_type',
        'value_2_id',
    ];

    public function comparitor(): BelongsTo
    {
        return $this->belongsTo(Comparitor::class);
    }

    public function value1(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'value_1_type', 'value_1_id');
    }

    public function value2(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'value_2_type', 'value_2_id');
    }
}
