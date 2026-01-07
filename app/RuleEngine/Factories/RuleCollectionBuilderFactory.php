<?php

declare(strict_types=1);

namespace App\RuleEngine\Factories;

use App\RuleEngine\Comparitors\ComparitorType;
use App\RuleEngine\Contracts\RuleCollectionBuilder;
use App\RuleEngine\DataSources\RuleConfigCollectionBuilder;
use App\RuleEngine\DataSources\RuleOrmCollectionBuilder;
use App\RuleEngine\RuleDto;
use App\RuleEngine\Values\ValueType;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class RuleCollectionBuilderFactory
{
    public const string SOURCE_TYPE_DB = 'db';

    public const string SOURCE_TYPE_FILE = 'file';

    /** @var Collection<int, RuleDto> */
    protected Collection $rules;

    protected string $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * @param  string  $type  Either "db" or "file"
     */
    public static function makeBuilder(string $source, string $type): RuleCollectionBuilder
    {
        return match ($type) {
            self::SOURCE_TYPE_DB => new RuleOrmCollectionBuilder($source),
            self::SOURCE_TYPE_FILE => new RuleConfigCollectionBuilder($source),
            default => throw new InvalidArgumentException("Invalid source type: {$type}"),
        };
    }

    protected function resolveComparitorType(string $comparitor): ComparitorType
    {
        return ComparitorType::from($comparitor);
    }

    protected function resolveValueType(string $type): ValueType
    {
        return ValueType::from($type);
    }
}
