<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    #[TestDox('event::keywords is mapped from event_categories, event_tags and user_groups')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{
                "event_categories": [
                    "Aktiviteter",
                    "För barn och ungdom",
                    "Lovaktiviteter",
                    "Prova på"
                ],
                "event_tags": [
                    "Helsingborgs Konserthus",
                    "jazz",
                    "Konsert"
                ],
                "user_groups": [
                    {
                        "id": 312,
                        "name": "Helsingborg Stad",
                        "slug": "helsingborg-stad"
                    },
                    {
                        "id": 6161,
                        "name": "Lov",
                        "slug": "lov"
                    }
                ]
            }',
            Schema::event()->keywords([
                Schema::definedTerm()->name('Aktiviteter')->inDefinedTermSet(Schema::definedTermSet()->name('event_categories')),
                Schema::definedTerm()->name('För barn och ungdom')->inDefinedTermSet(Schema::definedTermSet()->name('event_categories')),
                Schema::definedTerm()->name('Lovaktiviteter')->inDefinedTermSet(Schema::definedTermSet()->name('event_categories')),
                Schema::definedTerm()->name('Prova på')->inDefinedTermSet(Schema::definedTermSet()->name('event_categories')),
                Schema::definedTerm()->name('Helsingborgs Konserthus')->inDefinedTermSet(Schema::definedTermSet()->name('event_tags')),
                Schema::definedTerm()->name('jazz')->inDefinedTermSet(Schema::definedTermSet()->name('event_tags')),
                Schema::definedTerm()->name('Konsert')->inDefinedTermSet(Schema::definedTermSet()->name('event_tags')),
                Schema::definedTerm()->name('Helsingborg Stad')->inDefinedTermSet(Schema::definedTermSet()->name('user_groups')),
                Schema::definedTerm()->name('Lov')->inDefinedTermSet(Schema::definedTermSet()->name('user_groups'))
            ])
        );
    }

    #[TestDox('event::keywords([]) when no relevant data is present')]
    public function testItHandlesMissingData()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapKeywords(),
            '{"id": 123}',
            Schema::event()->keywords([])
        );
    }
}
