<?php

namespace App\RuleEngine;

use App\RuleEngine\Comparitors\ComparitorStack;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\Factories\ValueFactory;
use Illuminate\Support\Collection;

class RuleEvaluator
{
    private RuleCollectionBuilder $ruleStrategy;

    private ?RuleDto $failed = null;

    /** @var array<mixed> */
    private array $data;

    /** @var Collection<int, RuleDto> */
    private Collection $rules;

    /**
     * @param  Collection<int, mixed>  $data
     */
    public function __construct(
        Collection $data,
        RuleCollectionBuilder $ruleStrategy
    ) {
        $this->data = $data->all();
        $this->ruleStrategy = $ruleStrategy;
        $this->rules = $this->ruleStrategy->make();
    }

    public function resolve(string $ruleName): bool
    {
        $this->failed = null;

        /** @var RuleDto|null $ruleDto */
        $ruleDto = $this->rules->first(fn (RuleDto $rule) => $rule->name === $ruleName);

        if ($ruleDto === null) {
            throw new \RuntimeException("Rule not found: {$ruleName}");
        }

        $rule = $this->hydrateRule($ruleDto);

        $handler = ComparitorStack::get();

        if ($handler === null) {
            throw new \RuntimeException('No comparitor handlers available');
        }

        $result = $handler->handle($rule);

        if (! $result) {
            $this->failed = $rule;
        }

        return $result;
    }

    public function failed(): ?RuleDto
    {
        return $this->failed;
    }

    private function hydrateRule(RuleDto $ruleDto): RuleDto
    {
        $data = collect($this->data);

        $value1 = ValueFactory::makeValue(
            $ruleDto->value1Type,
            $data,
            $ruleDto->value1,
            $this->rules
        );

        $value2 = ValueFactory::makeValue(
            $ruleDto->value2Type,
            $data,
            $ruleDto->value2,
            $this->rules
        );

        return new RuleDto(
            name: $ruleDto->name,
            value1Type: $ruleDto->value1Type,
            value1: $value1,
            comparitorType: $ruleDto->comparitorType,
            value2Type: $ruleDto->value2Type,
            value2: $value2,
        );
    }
}
