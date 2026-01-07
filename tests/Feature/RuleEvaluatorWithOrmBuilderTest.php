<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Comparitor;
use App\Models\ReferenceValue;
use App\Models\Rule;
use App\Models\StaticValue;
use App\RuleEngine\DataSources\RuleOrmCollectionBuilder;
use App\RuleEngine\RuleEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RuleEvaluatorWithOrmBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_static_equals_passing(): void
    {
        $comparitor = Comparitor::factory()->equals()->create();
        $value1 = StaticValue::factory()->withValue(5)->create();
        $value2 = StaticValue::factory()->withValue(5)->create();

        Rule::factory()
            ->inCategory('test_category')
            ->create([
                'name' => 'db_equals_pass',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('test_category');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_equals_pass'));
    }

    public function test_static_equals_failing(): void
    {
        $comparitor = Comparitor::factory()->equals()->create();
        $value1 = StaticValue::factory()->withValue(5)->create();
        $value2 = StaticValue::factory()->withValue(10)->create();

        Rule::factory()
            ->inCategory('test_category')
            ->create([
                'name' => 'db_equals_fail',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('test_category');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertFalse($evaluator->resolve('db_equals_fail'));
        $this->assertNotNull($evaluator->failed());
    }

    public function test_greater_than_comparison(): void
    {
        $comparitor = Comparitor::factory()->greater()->create();
        $value1 = StaticValue::factory()->withValue(10)->create();
        $value2 = StaticValue::factory()->withValue(5)->create();

        Rule::factory()
            ->inCategory('comparisons')
            ->create([
                'name' => 'db_greater_pass',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('comparisons');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_greater_pass'));
    }

    public function test_less_than_comparison(): void
    {
        $comparitor = Comparitor::factory()->less()->create();
        $value1 = StaticValue::factory()->withValue(5)->create();
        $value2 = StaticValue::factory()->withValue(10)->create();

        Rule::factory()
            ->inCategory('comparisons')
            ->create([
                'name' => 'db_less_pass',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('comparisons');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_less_pass'));
    }

    public function test_reference_value_resolves_from_data(): void
    {
        $comparitor = Comparitor::factory()->greater()->create();
        $refValue = ReferenceValue::factory()->withNode('user.age')->create();
        $staticValue = StaticValue::factory()->withValue(18)->create();

        Rule::factory()
            ->inCategory('user_rules')
            ->create([
                'name' => 'db_age_check',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'reference_value',
                'value_1_id' => $refValue->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $staticValue->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('user_rules');
        $data = collect([
            'user' => [
                'age' => 25,
            ],
        ]);
        $evaluator = new RuleEvaluator($data, $builder);

        $this->assertTrue($evaluator->resolve('db_age_check'));
    }

    public function test_regex_comparison(): void
    {
        $comparitor = Comparitor::factory()->regex()->create();
        $value1 = StaticValue::factory()->withValue('hello world')->create();
        $value2 = StaticValue::factory()->withValue('/^hello/')->create();

        Rule::factory()
            ->inCategory('patterns')
            ->create([
                'name' => 'db_regex_match',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('patterns');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_regex_match'));
    }

    public function test_logical_and_operator(): void
    {
        $comparitor = Comparitor::factory()->all()->create();
        $value1 = StaticValue::factory()->withValue(true)->create();
        $value2 = StaticValue::factory()->withValue(true)->create();

        Rule::factory()
            ->inCategory('logic')
            ->create([
                'name' => 'db_both_true',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('logic');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_both_true'));
    }

    public function test_logical_or_operator(): void
    {
        $comparitor = Comparitor::factory()->any()->create();
        $value1 = StaticValue::factory()->withValue(false)->create();
        $value2 = StaticValue::factory()->withValue(true)->create();

        Rule::factory()
            ->inCategory('logic')
            ->create([
                'name' => 'db_either_true',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('logic');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_either_true'));
    }

    public function test_filters_by_category(): void
    {
        $comparitor = Comparitor::factory()->equals()->create();
        $value1 = StaticValue::factory()->withValue(5)->create();
        $value2 = StaticValue::factory()->withValue(5)->create();

        Rule::factory()
            ->inCategory('category_a')
            ->create([
                'name' => 'rule_in_a',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        Rule::factory()
            ->inCategory('category_b')
            ->create([
                'name' => 'rule_in_b',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builderA = new RuleOrmCollectionBuilder('category_a');
        $evaluatorA = new RuleEvaluator(collect(), $builderA);

        $this->assertTrue($evaluatorA->resolve('rule_in_a'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rule not found: rule_in_b');
        $evaluatorA->resolve('rule_in_b');
    }

    public function test_returns_empty_collection_for_nonexistent_category(): void
    {
        $builder = new RuleOrmCollectionBuilder('nonexistent_category');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Rule not found: any_rule');
        $evaluator->resolve('any_rule');
    }

    public function test_strict_comparison(): void
    {
        $comparitor = Comparitor::factory()->strict()->create();
        $value1 = StaticValue::factory()->withValue('hello')->create();
        $value2 = StaticValue::factory()->withValue('hello')->create();

        Rule::factory()
            ->inCategory('strict_tests')
            ->create([
                'name' => 'db_strict_pass',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('strict_tests');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_strict_pass'));
    }

    public function test_not_equals_comparison(): void
    {
        $comparitor = Comparitor::factory()->not()->create();
        $value1 = StaticValue::factory()->withValue(5)->create();
        $value2 = StaticValue::factory()->withValue(10)->create();

        Rule::factory()
            ->inCategory('not_tests')
            ->create([
                'name' => 'db_not_pass',
                'comparitor_id' => $comparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value1->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value2->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('not_tests');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('db_not_pass'));
    }

    public function test_multiple_rules_in_same_category(): void
    {
        $equalsComparitor = Comparitor::factory()->equals()->create();
        $greaterComparitor = Comparitor::factory()->greater()->create();

        $value5 = StaticValue::factory()->withValue(5)->create();
        $value10 = StaticValue::factory()->withValue(10)->create();

        Rule::factory()
            ->inCategory('multi_rules')
            ->create([
                'name' => 'equals_rule',
                'comparitor_id' => $equalsComparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value5->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value5->id,
            ]);

        Rule::factory()
            ->inCategory('multi_rules')
            ->create([
                'name' => 'greater_rule',
                'comparitor_id' => $greaterComparitor->id,
                'value_1_type' => 'static_value',
                'value_1_id' => $value10->id,
                'value_2_type' => 'static_value',
                'value_2_id' => $value5->id,
            ]);

        $builder = new RuleOrmCollectionBuilder('multi_rules');
        $evaluator = new RuleEvaluator(collect(), $builder);

        $this->assertTrue($evaluator->resolve('equals_rule'));
        $this->assertTrue($evaluator->resolve('greater_rule'));
    }
}