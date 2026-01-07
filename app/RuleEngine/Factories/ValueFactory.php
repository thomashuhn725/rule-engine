<?php

declare(strict_types=1);

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
     * @param  Collection<int, RuleDto>  $rules
     */
    public static function makeValue(
        ValueType $type,
        mixed $valRef,
        Collection $rules
    ): Value {
        return match ($type) {
            ValueType::Reference => new ReferenceValue($valRef, $rules),
            ValueType::Nested => new NestedValue($valRef, $rules),
            ValueType::Static => new StaticValue($valRef, $rules),
        };
    }
}
