<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\RuleDto;
use App\RuleEngine\RuleEvaluator;
use App\RuleEngine\Values\StaticValueResolver;
use Illuminate\Support\Collection;
use RuntimeException;
use Tests\TestCase;

class RuleEvaluatorTest extends TestCase
{
    private function createMockBuilder(Collection $rules): RuleCollectionBuilder
    {
        return new class ($rules) implements RuleCollectionBuilder
        {
            public function __construct(private readonly Collection $rules) {}

            public function make(): Collection
            {
                return $this->rules;
            }
        };
    }

    private function createStaticRule(string $name, mixed $value1, ComparitorType $comparitor, mixed $value2): RuleDto
    {
        $rules = collect();

        return new RuleDto(
            name: $name,
            value1: new StaticValueResolver($value1, $rules),
            comparitorType: $comparitor,
            value2: new StaticValueResolver($value2, $rules),
        );
    }

    public function test_equals_comparison_returns_true_when_values_match(): void
    {
        $rule = $this->createStaticRule('test_rule', 5, ComparitorType::Equals, 5);
        $builder = $this->createMockBuilder(collect([$rule]));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $result = $evaluator->resolve('test_rule');

        $this->assertTrue($result);
        $this->assertNull($evaluator->failed());
    }

    public function test_equals_comparison_returns_false_when_values_differ(): void
    {
        $rule = $this->createStaticRule('test_rule', 5, ComparitorType::Equals, 10);
        $builder = $this->createMockBuilder(collect([$rule]));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $result = $evaluator->resolve('test_rule');

        $this->assertFalse($result);
        $this->assertNotNull($evaluator->failed());
        $this->assertSame('test_rule', $evaluator->failed()->name);
    }

    public function test_strict_comparison_returns_true_for_identical_values(): void
    {
        $rule = $this->createStaticRule('strict_rule', '5', ComparitorType::Strict, '5');
        $builder = $this->createMockBuilder(collect([$rule]));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $result = $evaluator->resolve('strict_rule');

        $this->assertTrue($result);
    }

    public function test_strict_comparison_returns_false_for_type_mismatch(): void
    {
        $rule = $this->createStaticRule('strict_rule', 5, ComparitorType::Strict, '5');
        $builder = $this->createMockBuilder(collect([$rule]));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $result = $evaluator->resolve('strict_rule');

        $this->assertFalse($result);
    }

    public function test_throws_exception_when_rule_not_found(): void
    {
        $builder = $this->createMockBuilder(collect());
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rule not found: nonexistent_rule');

        $evaluator->resolve('nonexistent_rule');
    }

    public function test_failed_resets_between_evaluations(): void
    {
        $failingRule = $this->createStaticRule('fail_rule', 5, ComparitorType::Equals, 10);
        $passingRule = $this->createStaticRule('pass_rule', 5, ComparitorType::Equals, 5);
        $builder = $this->createMockBuilder(collect([$failingRule, $passingRule]));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $evaluator->resolve('fail_rule');
        $this->assertNotNull($evaluator->failed());

        $evaluator->resolve('pass_rule');
        $this->assertNull($evaluator->failed());
    }
}