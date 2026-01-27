<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('event::keywords is constructed from _embedded.acf:term')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Test term",
                            "taxonomy": "term_keyword"
                        },
                        {
                            "name": "event with blacklisted taxonomy",
                            "taxonomy": "organization"
                        },
                        {
                            "name": "Another term",
                            "taxonomy": "another_term_keyword"
                        }
                    ]
                }
            }',
            Schema::event()->keywords([
                Schema::definedTerm()->name('Test term')->inDefinedTermSet(Schema::definedTermSet()->name('term_keyword')),
                Schema::definedTerm()->name('Another term')->inDefinedTermSet(Schema::definedTermSet()->name('another_term_keyword')),
            ])
        );
    }

    #[TestDox('event::keywords([]) when _embedded.acf:term is missing')]
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
