<?php

declare(strict_types=1);

namespace App\RuleEngine\Values;

use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

abstract class ValueResolver
{
    protected mixed $valRef;

    protected mixed $cachedValue = null;

    /**
     * @param  Collection<int, RuleDto>  $rules
     */
    public function __construct(
        mixed $valRef,
        protected Collection $rules
    ) {
        $this->valRef = $valRef;
    }

    /**
     * Gets the value if it exists.
     * Returns true if value exists, false otherwise.
     * The actual value is passed by reference.
     *
     * @param  Collection<int, mixed>  $data
     */
    public function getValue(Collection $data, mixed &$value = null): bool
    {
        if (! $this->checkHasValue($data)) {
            return false;
        }

        if ($this->cachedValue === null) {
            $this->cachedValue = $this->findValue($data);
        }

        $value = $this->cachedValue;

        return true;
    }

    abstract public function getType(): ValueType;

    /**
     * @param  Collection<int, mixed>  $data
     */
    abstract protected function checkHasValue(Collection $data): bool;

    /**
     * @param  Collection<int, mixed>  $data
     */
    abstract protected function findValue(Collection $data): mixed;
}
