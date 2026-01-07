<?php

namespace App\RuleEngine;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\Value;
use App\RuleEngine\Values\ValueType;

class RuleDto
{
    public function __construct(
        public string $name,
        public ValueType $value1Type,
        public mixed $value1,
        public ComparitorType|string $comparitorType,
        public ValueType $value2Type,
        public mixed $value2,
    ) {
        if (is_string($this->comparitorType)) {
            $this->comparitorType = ComparitorType::fromSymbol($this->comparitorType);
        }
    }
}
