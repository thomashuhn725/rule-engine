<?php

declare(strict_types=1);

namespace App\RuleEngine\Comparitors\Handlers;

use App\RuleEngine\Comparitors\ComparitorHandler;
use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Values\ValueResolver;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Regex extends ComparitorHandler
{
    protected function compare(ValueResolver $value1, ValueResolver $value2, Collection $data): bool
    {
        $val1 = null;
        $val2 = null;

        $value1->getValue($data, $val1);
        $value2->getValue($data, $val2);

        $pattern = (string) $val2;

        if (strlen($pattern) < 2 || $pattern[0] !== $pattern[strlen($pattern) - 1]) {
            throw new InvalidArgumentException("Invalid regex pattern: {$pattern}");
        }

        return preg_match($pattern, (string) $val1) === 1;
    }

    protected function getComparitorType(): ComparitorType
    {
        return ComparitorType::Regex;
    }
}
