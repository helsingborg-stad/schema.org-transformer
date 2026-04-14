<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapOrganizer::class)]
final class MapOrganizerTest extends TestCase
{
    #[TestDox('event::organizer is taken from $.location')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{
                "location": {
                    "value": "Some Organizer"
                }
            }',
            Schema::event()->organizer([
                Schema::organization()->name('Some Organizer')
            ])
        );
    }

    #[TestDox('event::organizer([]) when $.location is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{
                "id": 123
            }',
            Schema::event()->organizer([])
        );
    }
}
