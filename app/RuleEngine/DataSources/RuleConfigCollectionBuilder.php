<?php

namespace App\RuleEngine\DataSources;

use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\ValueType;
use Illuminate\Support\Collection;
use stdClass;

class RuleConfigCollectionBuilder implements RuleCollectionBuilder
{
    public function __construct(
        private string $source
    ) {}

    /**
     * @return Collection<int, RuleDto>
     */
    public function make(): Collection
    {
        $rules = $this->get($this->source);

        if ($rules instanceof stdClass) {
            return collect(get_object_vars($rules))->map(
                fn (stdClass $rule, string $name) => $this->makeRule($name, $rule)
            )->values();
        }

        return collect($rules)->map(
            fn (stdClass $rule, int|string $key) => $this->makeRule(
                is_string($key) ? $key : "rule_{$key}",
                $rule
            )
        )->values();
    }

    /**
     * @return stdClass|stdClass[]
     */
    private function get(string $path): stdClass|array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException("Unable to read config file: {$path}");
        }

        $decoded = json_decode($contents);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in config file: {$path}");
        }

        return $decoded;
    }

    private function makeRule(string $name, stdClass $rule): RuleDto
    {
        [$value1Type, $value1] = $this->resolveValue($rule, 1);
        [$value2Type, $value2] = $this->resolveValue($rule, 2);

        return new RuleDto(
            name: $name,
            value1Type: $value1Type,
            value1: $value1,
            comparitorType: $rule->comparitor,
            value2Type: $value2Type,
            value2: $value2,
        );
    }

    /**
     * @return array{ValueType, mixed}
     */
    private function resolveValue(stdClass $rule, int $position): array
    {
        $staticKey = "static{$position}";
        $nestedKey = "nested{$position}";
        $referenceKey = "reference{$position}";

        if (property_exists($rule, $staticKey) && $rule->$staticKey !== null) {
            return [ValueType::Static, $rule->$staticKey];
        }

        if (property_exists($rule, $nestedKey) && $rule->$nestedKey !== null) {
            return [ValueType::Nested, $rule->$nestedKey];
        }

        if (property_exists($rule, $referenceKey) && $rule->$referenceKey !== null) {
            return [ValueType::Reference, $rule->$referenceKey];
        }

        throw new \RuntimeException("No value found for position {$position} in rule");
    }
}
