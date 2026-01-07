<?php

declare(strict_types=1);

namespace App\RuleEngine\DataSources;

use App\Models\Rule;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\Factories\RuleCollectionBuilderFactory;
use App\RuleEngine\Factories\ValueFactory;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\ValueType;
use Illuminate\Support\Collection;

class RuleOrmCollectionBuilder extends RuleCollectionBuilderFactory implements RuleCollectionBuilder
{
    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection
    {
        $this->rules = $this->get($this->source)->map(
            fn (Rule $model) => $this->makeRule($model)
        );

        return $this->rules;
    }

    /**
     * @return Collection<int, Rule>
     */
    private function get(string $source): Collection
    {
        return Rule::query()
            ->with(['comparitor', 'value1', 'value2'])
            ->where('category', $source)
            ->get();
    }

    private function makeRule(Rule $model): RuleDto
    {
        $rules = $this->rules ?? collect();

        return new RuleDto(
            name: $model->name,
            value1: ValueFactory::makeValue(
                $this->mapValueType($model->value_1_type),
                $this->extractValue($model->value1),
                $rules
            ),
            comparitorType: $this->resolveComparitorType($model->comparitor->symbol),
            value2: ValueFactory::makeValue(
                $this->mapValueType($model->value_2_type),
                $this->extractValue($model->value2),
                $rules
            ),
        );
    }

    private function mapValueType(string $type): ValueType
    {
        return match ($type) {
            'reference_value' => ValueType::Reference,
            'nested_value' => ValueType::Nested,
            'static_value' => ValueType::Static,
            default => throw new \InvalidArgumentException("Unknown value type: {$type}"),
        };
    }

    private function extractValue(mixed $valueModel): mixed
    {
        return match (true) {
            $valueModel instanceof \App\Models\StaticValue => $valueModel->value,
            $valueModel instanceof \App\Models\ReferenceValue => $valueModel->node,
            $valueModel instanceof \App\Models\NestedValue => $valueModel->rule->name,
            default => null,
        };
    }
}
