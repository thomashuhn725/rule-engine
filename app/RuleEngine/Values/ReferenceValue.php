<?php

namespace App\RuleEngine\Values;

class ReferenceValue extends Value
{
    public function getType(): ValueType
    {
        return ValueType::Reference;
    }

    protected function checkHasValue(): bool
    {
        return ArrayHelper::search($this->data, (string) $this->valRef);
    }

    protected function findValue(): mixed
    {
        $match = null;
        ArrayHelper::search($this->data, (string) $this->valRef, $match);

        return $match;
    }
}
