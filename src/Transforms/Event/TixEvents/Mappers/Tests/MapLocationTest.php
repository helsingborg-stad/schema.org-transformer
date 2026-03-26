<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('event::location is taken from first source->Dates->Venue/Hall')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "EventGroupId": 123,
                "Dates": [
                    {
                        "EventId": 1,
                        "DefaultEventGroupId": 123,
                        "Venue": "Rådhuset",
                        "Hall": "Rådssalen"
                    },
                    {
                        "EventId": 2,
                        "DefaultEventGroupId": 123,
                        "Venue": "Kulturhuset",
                        "Hall": "Stora salen"
                    }
                ]
            }',
            Schema::event()->location([
                Schema::place()
                    ->name('Rådhuset')
                    ->description('Rådssalen')
            ])
        );
    }

    #[TestDox('event::location is not set if there are no source->Dates->Venue/Hall')]
    public function testNoLocation()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "EventGroupId": 123
            }',
            Schema::event()->location(null),
            'Did not expect to find location in source'
        );
    }
}
