<?php

declare(strict_types=1);

namespace App\RuleEngine;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\Value;

class RuleDto
{
    public function __construct(
        public string $name,
        public Value $value1,
        public ComparitorType $comparitorType,
        public Value $value2,
    ) {}
}
