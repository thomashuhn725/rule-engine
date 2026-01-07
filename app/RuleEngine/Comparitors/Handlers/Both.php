<?php

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\Value;

class Both extends ComparitorHandler
{
    protected function compare(Value $value1, Value $value2): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($val1);
        $value2->getValue($val2);

        if ($val1 instanceof RuleDto && $val2 instanceof RuleDto) {
            $result1 = $this->next?->handle($val1) ?? false;
            $result2 = $this->next?->handle($val2) ?? false;

            return $result1 && $result2;
        }

        return (bool) $val1 && (bool) $val2;
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::All;
    }
}
