<?php

namespace App\Models;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Values\ValueType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuleModel extends Model
{
    /** @use HasFactory<\Database\Factories\RuleModelFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'value_1_type',
        'value_1',
        'comparitor',
        'value_2_type',
        'value_2',
        'category',
    ];

    public function name(): string
    {
        return $this->name;
    }

    public function value1Type(): ValueType
    {
        return ValueType::from($this->value_1_type);
    }

    public function value1(): mixed
    {
        return $this->value_1;
    }

    public function comparitor(): ComparitorHandler
    {
        return ComparitorHandler::fromSymbol($this->comparitor);
    }

    public function value2Type(): ValueType
    {
        return ValueType::from($this->value_2_type);
    }

    public function value2(): mixed
    {
        return $this->value_2;
    }
}
