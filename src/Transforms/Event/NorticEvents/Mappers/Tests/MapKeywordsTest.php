<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('event::keywords are mapped from comma-separated tags and category')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{"tags":"Dansteater, Dansutbildning","category":"Dans"}',
            Schema::event()->keywords([
                Schema::definedTerm()->name('Dansteater')->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
                Schema::definedTerm()->name('Dansutbildning')->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
                Schema::definedTerm()->name('Dans')->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
            ])
        );
    }

    #[TestDox('event::keywords are empty when tags and category are missing')]
    public function testNoKeywords(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{}',
            Schema::event()->keywords([])
        );
    }
}
