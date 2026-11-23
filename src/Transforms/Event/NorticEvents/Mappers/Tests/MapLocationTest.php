<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('event::location is taken from the first source show arena')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{"shows":[{"arenaName":"Häftiga stället","arenaAddress":"Coolgatan 12","arenaLatitude":"57.1339131","arenaLongitude":"12.7118231"}]}',
            Schema::event()->location([
                Schema::place()
                    ->name('Häftiga stället')
                    ->address('Coolgatan 12')
                    ->latitude('57.1339131')
                    ->longitude('12.7118231')
            ])
        );
    }

    #[TestDox('event::location is empty when no show has an arena')]
    public function testNoLocation(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{"shows":[]}',
            Schema::event()->location([])
        );
    }
}
