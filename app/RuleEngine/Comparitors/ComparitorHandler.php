<?php

namespace App\RuleEngine\Comparitors;

use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\Value;

abstract class ComparitorHandler
{
    public ?self $next = null;

    public function handle(RuleDto $rule): bool
    {
        if ($rule->comparitorType === $this->getComparitorType()) {
            $value1 = null;
            $value2 = null;

            $hasValue1 = $rule->value1->getValue($value1);
            $hasValue2 = $rule->value2->getValue($value2);

            if (! $hasValue1 || ! $hasValue2) {
                return false;
            }

            return $this->compare($rule->value1, $rule->value2);
        }

        if ($this->next !== null) {
            return $this->next->handle($rule);
        }

        throw new \RuntimeException("No handler found for comparitor type: {$rule->comparitorType->value}");
    }

    abstract protected function compare(Value $value1, Value $value2): bool;

    abstract protected function getComparitorType(): ComparitorType;

    public static function fromSymbol(string $symbol): self
    {
        return ComparitorStack::make()->get();
    }
}
