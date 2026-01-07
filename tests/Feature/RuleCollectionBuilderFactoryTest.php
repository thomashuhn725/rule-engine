<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\RuleEngine\DataSources\RuleConfigCollectionBuilder;
use App\RuleEngine\DataSources\RuleOrmCollectionBuilder;
use App\RuleEngine\Factories\RuleCollectionBuilderFactory;
use InvalidArgumentException;
use Tests\TestCase;

class RuleCollectionBuilderFactoryTest extends TestCase
{
    private function fixturesPath(string $file): string
    {
        return base_path('tests/fixtures/rules/'.$file);
    }

    public function test_creates_config_builder_for_file_type(): void
    {
        $builder = RuleCollectionBuilderFactory::makeBuilder(
            $this->fixturesPath('static_equals.json'),
            RuleCollectionBuilderFactory::SOURCE_TYPE_FILE
        );

        $this->assertInstanceOf(RuleConfigCollectionBuilder::class, $builder);
    }

    public function test_creates_orm_builder_for_db_type(): void
    {
        $builder = RuleCollectionBuilderFactory::makeBuilder(
            'test_category',
            RuleCollectionBuilderFactory::SOURCE_TYPE_DB
        );

        $this->assertInstanceOf(RuleOrmCollectionBuilder::class, $builder);
    }

    public function test_throws_exception_for_invalid_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid source type: invalid');

        RuleCollectionBuilderFactory::makeBuilder('source', 'invalid');
    }
}