<?php

namespace App\RuleEngine\Values;

class StaticValue extends Value
{
    protected bool $hasValue = true;

    public function getType(): ValueType
    {
        return ValueType::Static;
    }

    protected function checkHasValue(): bool
    {
        return true;
    }

    protected function findValue(): mixed
    {
        return $this->valRef;
    }
}
