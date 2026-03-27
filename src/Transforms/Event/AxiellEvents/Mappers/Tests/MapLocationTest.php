<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('event::location is taken from $.location and $.room')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "location": {
                    "value": "Some Location"
                },
                "room": {
                    "value": "Some Room"
                }
            }',
            Schema::event()->location([Schema::place()->name('Some Location')->description('Some Room')])
        );
    }

    #[TestDox('event::location is taken from $.location')]
    public function testLocationOnly()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "location": {
                    "value": "Some Location"
                }
            }',
            Schema::event()->location([Schema::place()->name('Some Location')->description(null)])
        );
    }

    #[TestDox('event::location(null) when $.location is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "id": 123,
                "room": {
                    "value": "Some Room"
                }
            }',
            Schema::event()->location([])
        );
    }
}
