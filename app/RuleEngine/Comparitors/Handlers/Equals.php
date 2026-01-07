<?php

declare(strict_types=1);

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\Value;
use Illuminate\Support\Collection;

class Equals extends ComparitorHandler
{
    protected function compare(Value $value1, Value $value2, Collection $data): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($data, $val1);
        $value2->getValue($data, $val2);

        return $val1 == $val2;
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::Equals;
    }
}
