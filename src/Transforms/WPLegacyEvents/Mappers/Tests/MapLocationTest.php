<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('event::location is mapped from location')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "location": {
                    "id": 1851492,
                    "title": "Rydebäcks idrottshall",
                    "parent": null,
                    "content": "",
                    "street_address": "Frösögatan 15",
                    "postal_code": "25730",
                    "city": "Rydebäck",
                    "country": "",
                    "formatted_address": "Frösögatan 15, 25730, Rydebäck",
                    "latitude": "55.9668958",
                    "longitude": "12.7745249"
                }
            }',
            Schema::event()->location(Schema::place()
                ->name("Rydebäcks idrottshall")
                ->address("Frösögatan 15, 25730, Rydebäck")
                ->latitude("55.9668958")
                ->longitude("12.7745249"))
        );
    }

    #[TestDox('event::location(null) when location is missing')]
    public function testHandlesMissingLocation()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{"id": 123}',
            Schema::event()->location(null)
        );
    }
}
