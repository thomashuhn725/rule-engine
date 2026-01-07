<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comparitor extends Model
{
    /** @use HasFactory<\Database\Factories\ComparitorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
    ];

    public function rules(): HasMany
    {
        return $this->hasMany(Rule::class);
    }
}
