<?php

namespace App\RuleEngine\Contracts;

use App\RuleEngine\RuleDto;
use Illuminate\Support\Collection;

interface RuleCollectionBuilder
{
    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection;
}
