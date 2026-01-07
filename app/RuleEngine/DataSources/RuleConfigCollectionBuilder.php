<?php

declare(strict_types=1);

namespace App\RuleEngine\DataSources;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\Factories\RuleCollectionBuilderFactory;
use App\RuleEngine\Factories\ValueFactory;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\Value;
use App\RuleEngine\Values\ValueType;
use Illuminate\Support\Collection;
use RuntimeException;
use stdClass;

class RuleConfigCollectionBuilder extends RuleCollectionBuilderFactory implements RuleCollectionBuilder
{
    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection
    {
        $rawRules = $this->get($this->source);

        if ($rawRules instanceof stdClass) {
            $this->rules = collect(get_object_vars($rawRules))->map(
                fn (stdClass $rule, string $name) => $this->makeRule($name, $rule)
            )->values();
        } else {
            $this->rules = collect($rawRules)->map(
                fn (stdClass $rule, int|string $key) => $this->makeRule(
                    is_string($key) ? $key : "rule_{$key}",
                    $rule
                )
            )->values();
        }

        return $this->rules;
    }

    /**
     * @return stdClass|stdClass[]
     */
    private function get(string $path): stdClass|array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read config file: {$path}");
        }

        $decoded = json_decode($contents);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Invalid JSON in config file: {$path}");
        }

        return $decoded;
    }

    private function makeRule(string $name, stdClass $rule): RuleDto
    {
        $rules = $this->rules ?? collect();

        return new RuleDto(
            name: $name,
            value1: $this->resolveValue($rule, 1, $rules),
            comparitorType: ComparitorType::from($rule->comparitor),
            value2: $this->resolveValue($rule, 2, $rules),
        );
    }

    /**
     * @param  Collection<int, RuleDto>  $rules
     */
    private function resolveValue(stdClass $rule, int $position, Collection $rules): Value
    {
        $staticKey = "static{$position}";
        $nestedKey = "nested{$position}";
        $referenceKey = "reference{$position}";

        if (property_exists($rule, $staticKey) && $rule->$staticKey !== null) {
            return ValueFactory::makeValue(ValueType::Static, $rule->$staticKey, $rules);
        }

        if (property_exists($rule, $nestedKey) && $rule->$nestedKey !== null) {
            return ValueFactory::makeValue(ValueType::Nested, $rule->$nestedKey, $rules);
        }

        if (property_exists($rule, $referenceKey) && $rule->$referenceKey !== null) {
            return ValueFactory::makeValue(ValueType::Reference, $rule->$referenceKey, $rules);
        }

        throw new RuntimeException("No value found for position {$position} in rule");
    }
}
