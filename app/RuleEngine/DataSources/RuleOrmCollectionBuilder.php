<?php

namespace App\RuleEngine\DataSources;

use App\Models\RuleModel;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

class RuleOrmCollectionBuilder implements RuleCollectionBuilder
{
    public function __construct(
        private string $source
    ) {}

    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection
    {
        return $this->get($this->source)->map(
            fn (RuleModel $model) => $this->makeRule($model)
        );
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
        return new RuleDto(
            name: $model->name(),
            value1Type: $model->value1Type(),
            value1: $model->value1(),
            comparitorType: $model->comparitor(),
            value2Type: $model->value2Type(),
            value2: $model->value2(),
        );
    }
}
