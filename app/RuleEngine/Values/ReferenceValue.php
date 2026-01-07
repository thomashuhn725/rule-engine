<?php

declare(strict_types=1);

namespace App\RuleEngine\Values;

use Illuminate\Support\Collection;

class ReferenceValue extends Value
{
    public function getType(): ValueType
    {
        return ValueType::Reference;
    }

    protected function checkHasValue(Collection $data): bool
    {
        return ArrayHelper::search($data->all(), (string) $this->valRef);
    }

    protected function findValue(Collection $data): mixed
    {
        $match = null;
        ArrayHelper::search($data->all(), (string) $this->valRef, $match);

        return $match;
    }
}
