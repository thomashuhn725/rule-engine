<?php

declare(strict_types=1);

namespace App\RuleEngine\DataSources;

use App\Models\RuleModel;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\Factories\RuleCollectionBuilderFactory;
use App\RuleEngine\Factories\ValueFactory;
use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

class RuleOrmCollectionBuilder extends RuleCollectionBuilderFactory implements RuleCollectionBuilder
{
    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection
    {
        $this->rules = $this->get($this->source)->map(
            fn (RuleModel $model) => $this->makeRule($model)
        );

        return $this->rules;
    }

    /**
     * @return Collection<int, RuleModel>
     */
    private function get(string $source): Collection
    {
        return RuleModel::query()
            ->where('category', $source)
            ->get();
    }

    private function makeRule(RuleModel $model): RuleDto
    {
        $rules = $this->rules ?? collect();

        return new RuleDto(
            name: $model->name(),
            value1: ValueFactory::makeValue($model->value1Type(), $model->value1(), $rules),
            comparitorType: $this->resolveComparitorType($model->comparitor),
            value2: ValueFactory::makeValue($model->value2Type(), $model->value2(), $rules),
        );
    }
}
