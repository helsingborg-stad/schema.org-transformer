<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapOrganizer;

#[CoversClass(MapOrganizer::class)]
final class MapOrganizerTest extends TestCase
{
    #[TestDox('event::organizer is taken from $.organizerName')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{"organizerName":"Balettakademien"}',
            Schema::event()->organizer([Schema::organization()->name('Balettakademien')])
        );
    }

    #[TestDox('event::organizer is empty when no organizer is provided')]
    public function testNoOrganizer(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{}',
            Schema::event()->organizer([])
        );
    }
}
