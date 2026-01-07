<?php

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\Value;

class Regex extends ComparitorHandler
{
    protected function compare(Value $value1, Value $value2): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($val1);
        $value2->getValue($val2);

        return (bool) preg_match((string) $val2, (string) $val1);
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::Regex;
    }
}
