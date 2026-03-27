<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('event::keywords is taken from $.tags')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "tags": ["tag1", "tag2"]
            }',
            Schema::event()->keywords([
                Schema::definedTerm()
                        ->name('tag1')
                        ->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
                Schema::definedTerm()
                        ->name('tag2')
                        ->inDefinedTermSet(Schema::definedTermSet()->name('tags'))
            ])
        );
    }

    #[TestDox('event::keywords([]) when $.tags is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "id": 123
            }',
            Schema::event()->keywords([])
        );
    }
}
