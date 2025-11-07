<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    #[TestDox('preschool::location is taken from acf.visiting_address')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapLocation(),
            '{
                "acf": {
                    "visiting_address": [{
                        "address": {
                            "name": "Testskolan",
                            "address": "Testskolan, Skolgatan 1",
                            "lat": 1.234,
                            "lng": 5.678
                        }
                    }]
                }
            }',
            Schema::preschool()
                ->location([
                    Schema::place()
                        ->name("Testskolan")
                        ->address("Testskolan, Skolgatan 1")
                        ->latitude(1.234)
                        ->longitude(5.678)])
        );
    }
}
