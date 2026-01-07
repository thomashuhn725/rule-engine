<?php

declare(strict_types=1);

namespace App\RuleEngine\Values;

use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

class NestedValue extends Value
{
    public function getType(): ValueType
    {
        return ValueType::Nested;
    }

    protected function checkHasValue(Collection $data): bool
    {
        $ruleName = (string) $this->valRef;

        return $this->rules->contains(fn (RuleDto $rule) => $rule->name === $ruleName);
    }

    protected function findValue(Collection $data): mixed
    {
        $ruleName = (string) $this->valRef;

        /** @var RuleDto|null $rule */
        $rule = $this->rules->first(fn (RuleDto $rule) => $rule->name === $ruleName);

        return $rule;
    }
}
