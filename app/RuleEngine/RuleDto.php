<?php

declare(strict_types=1);

namespace App\RuleEngine;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\ValueResolver;

class RuleDto
{
    public function __construct(
        public string         $name,
        public ValueResolver  $value1,
        public ComparitorType $comparitorType,
        public ValueResolver  $value2,
    ) {}
}
