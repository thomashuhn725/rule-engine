<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ReferenceValue extends Model
{
    /** @use HasFactory<\Database\Factories\ReferenceValueFactory> */
    use HasFactory;

    protected $fillable = [
        'node',
    ];

    public function rulesAsValue1(): MorphMany
    {
        return $this->morphMany(Rule::class, 'value1', 'value_1_type', 'value_1_id');
    }

    public function rulesAsValue2(): MorphMany
    {
        return $this->morphMany(Rule::class, 'value2', 'value_2_type', 'value_2_id');
    }
}
