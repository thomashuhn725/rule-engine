<?php

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\Value;

class Not extends ComparitorHandler
{
    protected function compare(Value $value1, Value $value2): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($val1);
        $value2->getValue($val2);

        if ($val1 instanceof RuleDto) {
            return ! ($this->next?->handle($val1) ?? false);
        }

        return $val1 != $val2;
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::Not;
    }
}
