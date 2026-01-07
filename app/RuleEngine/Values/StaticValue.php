<?php

declare(strict_types=1);

namespace App\RuleEngine\Values;

use Illuminate\Support\Collection;

class StaticValue extends Value
{
    public function getType(): ValueType
    {
        return ValueType::Static;
    }

    protected function checkHasValue(Collection $data): bool
    {
        return true;
    }

    protected function findValue(Collection $data): mixed
    {
        return $this->valRef;
    }
}
