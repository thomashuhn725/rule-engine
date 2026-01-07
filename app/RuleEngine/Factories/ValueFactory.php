<?php

declare(strict_types=1);

namespace App\RuleEngine\Factories;

use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\NestedValueResolver;
use App\RuleEngine\Values\ReferenceValueResolver;
use App\RuleEngine\Values\StaticValueResolver;
use App\RuleEngine\Values\ValueResolver;
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
    ): ValueResolver {
        return match ($type) {
            ValueType::Reference => new ReferenceValueResolver($valRef, $rules),
            ValueType::Nested => new NestedValueResolver($valRef, $rules),
            ValueType::Static => new StaticValueResolver($valRef, $rules),
        };
    }
}
