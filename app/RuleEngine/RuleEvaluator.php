<?php

declare(strict_types=1);

namespace App\RuleEngine;

use App\RuleEngine\Comparitors\ComparitorStack;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use Illuminate\Support\Collection;
use RuntimeException;

class RuleEvaluator
{
    private RuleCollectionBuilder $ruleStrategy;

    private ?RuleDto $failed = null;

    /** @var Collection<int, mixed> */
    private Collection $data;

    /** @var Collection<int, RuleDto> */
    private Collection $rules;

    /**
     * @param  Collection<int, mixed>  $data
     */
    public function __construct(
        Collection $data,
        RuleCollectionBuilder $ruleStrategy
    ) {
        $this->data = $data;
        $this->ruleStrategy = $ruleStrategy;
        $this->rules = $this->ruleStrategy->make();
    }

    public function resolve(string $ruleName): bool
    {
        $this->failed = null;

        /** @var RuleDto|null $rule */
        $rule = $this->rules->first(fn (RuleDto $rule) => $rule->name === $ruleName);

        if ($rule === null) {
            throw new RuntimeException("Rule not found: {$ruleName}");
        }

        $handler = ComparitorStack::get();

        if ($handler === null) {
            throw new RuntimeException('No comparitor handlers available');
        }

        $result = $handler->handle($rule, $this->data);

        if (! $result) {
            $this->failed = $rule;
        }

        return $result;
    }

    public function failed(): ?RuleDto
    {
        return $this->failed;
    }
}
