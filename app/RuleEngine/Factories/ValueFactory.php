<?php

namespace App\RuleEngine\Factories;

use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\NestedValue;
use App\RuleEngine\Values\ReferenceValue;
use App\RuleEngine\Values\StaticValue;
use App\RuleEngine\Values\Value;
use App\RuleEngine\Values\ValueType;
use Illuminate\Support\Collection;

class ValueFactory
{
    /**
     * @param  Collection<int, mixed>  $data
     * @param  Collection<int, RuleDto>  $rules
     */
    public static function makeValue(
        ValueType $type,
        Collection $data,
        mixed $valRef,
        Collection $rules
    ): Value {
        return match ($type) {
            ValueType::Reference => new ReferenceValue($data, $valRef, $rules),
            ValueType::Nested => new NestedValue($data, $valRef, $rules),
            ValueType::Static => new StaticValue($data, $valRef, $rules),
        };
    }
}
