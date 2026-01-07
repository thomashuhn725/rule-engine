<?php

declare(strict_types=1);

namespace App\RuleEngine\Comparitors;

use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\ValueResolver;
use Illuminate\Support\Collection;
use RuntimeException;

abstract class ComparitorHandler
{
    public ?self $next = null;

    /**
     * @param  Collection<int, mixed>  $data
     */
    public function handle(RuleDto $rule, Collection $data): bool
    {
        if ($rule->comparitorType === $this->getComparitorType()) {
            $value1 = null;
            $value2 = null;

            $hasValue1 = $rule->value1->getValue($data, $value1);
            $hasValue2 = $rule->value2->getValue($data, $value2);

            if (! $hasValue1 || ! $hasValue2) {
                return false;
            }

            return $this->compare($rule->value1, $rule->value2, $data);
        }

        if ($this->next !== null) {
            return $this->next->handle($rule, $data);
        }

        throw new RuntimeException("No handler found for comparitor type: {$rule->comparitorType->value}");
    }

    /**
     * @param  Collection<int, mixed>  $data
     */
    abstract protected function compare(ValueResolver $value1, ValueResolver $value2, Collection $data): bool;

    abstract protected function getComparitorType(): ComparitorType;
}
