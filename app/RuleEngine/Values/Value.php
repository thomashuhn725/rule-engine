<?php

namespace App\RuleEngine\Values;

use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

abstract class Value
{
    /** @var array<mixed> */
    protected array $data;

    protected mixed $valRef;

    protected bool $hasValue;

    protected mixed $cachedValue = null;

    /**
     * @param  Collection<int, mixed>  $data
     * @param  Collection<int, RuleDto>  $rules
     */
    public function __construct(
        Collection $data,
        mixed $valRef,
        protected Collection $rules
    ) {
        $this->data = $data->all();
        $this->valRef = $valRef;
        $this->hasValue = $this->checkHasValue();
    }

    /**
     * Gets the value if it exists.
     * Returns true if value exists, false otherwise.
     * The actual value is passed by reference.
     */
    public function getValue(mixed &$value = null): bool
    {
        if (! $this->hasValue) {
            return false;
        }

        $this->cacheValue();
        $value = $this->cachedValue;

        return true;
    }

    abstract public function getType(): ValueType;

    protected function cacheValue(): void
    {
        if ($this->cachedValue === null && $this->hasValue) {
            $this->cachedValue = $this->findValue();
        }
    }

    abstract protected function checkHasValue(): bool;

    abstract protected function findValue(): mixed;
}
