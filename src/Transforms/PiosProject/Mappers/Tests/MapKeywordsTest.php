<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('project::keywords is taken from tags')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "tags": [
                    {"displayName": "Tag 1"},
                    {"displayName": "Tag 2"},
                    {"displayName": null},
                    {},
                    null
                ]
            }',
            Schema::project()->keywords([
                Schema::definedTerm()->name('Tag 1')->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
                Schema::definedTerm()->name('Tag 2')->inDefinedTermSet(Schema::definedTermSet()->name('tags'))
            ])
        );
    }

    #[TestDox('project::keywords is empty if tags are missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{"id": 123}',
            Schema::project()->keywords([])
        );
    }
}
