<?php

namespace App\RuleEngine\Values;

enum ValueType: string
{
    case Reference = 'reference';
    case Nested = 'nested';
    case Static = 'static';
}
