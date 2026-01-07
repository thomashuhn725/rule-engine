<?php

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\Value;

class Less extends ComparitorHandler
{
    protected function compare(Value $value1, Value $value2): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($val1);
        $value2->getValue($val2);

        return $val1 < $val2;
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::Less;
    }
}
