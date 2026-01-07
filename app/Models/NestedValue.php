<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NestedValue extends Model
{
    /** @use HasFactory<\Database\Factories\NestedValueFactory> */
    use HasFactory;

    protected $fillable = [
        'rule_id',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }

    public function rulesAsValue1(): MorphMany
    {
        return $this->morphMany(Rule::class, 'value1', 'value_1_type', 'value_1_id');
    }

    public function rulesAsValue2(): MorphMany
    {
        return $this->morphMany(Rule::class, 'value2', 'value_2_type', 'value_2_id');
    }
}
