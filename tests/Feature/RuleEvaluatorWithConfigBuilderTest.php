<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\RuleEngine\DataSources\RuleConfigCollectionBuilder;
use App\RuleEngine\RuleEvaluator;
use RuntimeException;
use Tests\TestCase;

class RuleEvaluatorWithConfigBuilderTest extends TestCase
{
    private function fixturesPath(string $file): string
    {
        return base_path('tests/fixtures/rules/'.$file);
    }

    public function test_static_equals_passing(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_equals.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('equals_pass'));
        $this->assertNull($evaluator->failed());
    }

    public function test_static_equals_failing(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_equals.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertFalse($evaluator->resolve('equals_fail'));
        $this->assertNotNull($evaluator->failed());
        $this->assertSame('equals_fail', $evaluator->failed()->name);
    }

    public function test_greater_than_comparison(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_comparisons.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('greater_pass'));
        $this->assertFalse($evaluator->resolve('greater_fail'));
    }

    public function test_less_than_comparison(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_comparisons.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('less_pass'));
        $this->assertFalse($evaluator->resolve('less_fail'));
    }

    public function test_not_equals_comparison(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_comparisons.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('not_pass'));
        $this->assertFalse($evaluator->resolve('not_fail'));
    }

    public function test_strict_comparison(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('static_comparisons.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('strict_pass'));
        $this->assertFalse($evaluator->resolve('strict_fail'));
    }

    public function test_regex_matching(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('regex_rules.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('regex_match'));
        $this->assertFalse($evaluator->resolve('regex_no_match'));
    }

    public function test_logical_and_operator(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('logical_operators.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('both_true'));
        $this->assertFalse($evaluator->resolve('both_one_false'));
    }

    public function test_logical_or_operator(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('logical_operators.json'));
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('either_one_true'));
        $this->assertFalse($evaluator->resolve('either_both_false'));
    }

    public function test_reference_value_resolves_from_data(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('reference_values.json'));
        $data = collect([
            'user' => [
                'name' => 'John',
                'age' => 25,
            ],
        ]);
        $evaluator = new RuleEvaluator($data, $builder);

        $this->assertTrue($evaluator->resolve('ref_greater_static'));
        $this->assertTrue($evaluator->resolve('ref_equals_static'));
    }

    public function test_reference_value_with_nested_path(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('reference_values.json'));
        $data = collect([
            'order' => [
                'items' => [
                    ['name' => 'Product', 'price' => 99.99],
                ],
            ],
        ]);
        $evaluator = new RuleEvaluator($data, $builder);

        $this->assertTrue($evaluator->resolve('ref_nested_path'));
    }

    public function test_reference_value_returns_false_when_key_not_found(): void
    {
        $builder = new RuleConfigCollectionBuilder($this->fixturesPath('reference_values.json'));
        $data = collect([
            'user' => [
                'email' => 'test@example.com',
            ],
        ]);
        $evaluator = new RuleEvaluator($data, $builder);

        $this->assertFalse($evaluator->resolve('ref_greater_static'));
    }

    public function test_throws_exception_for_invalid_json(): void
    {
        $invalidPath = $this->fixturesPath('nonexistent.json');

        $this->expectException(RuntimeException::class);

        $builder = new RuleConfigCollectionBuilder($invalidPath);
        $builder->make();
    }
}