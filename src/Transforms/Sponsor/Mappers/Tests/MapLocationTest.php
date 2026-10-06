<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    /** Verify that offering location details become a postal address. */
    public function testMapsOfferingLocation(): void
    {
        $data = TestHelper::fixture('offering.data.json');

        $location = (new MapLocation())->map($data);

        self::assertEquals(
            Schema::place()
                ->address(Schema::postalAddress()
                    ->streetAddress('')
                    ->addressLocality('Exempelstad')
                    ->addressRegion('Exempellän')
                    ->postalCode('123 45')
                    ->addressCountry('Sverige'))
                ->description('Exempelvägen 1, 123 45 Exempelstad, Sverige'),
            $location
        );
    }

    /** Verify the current empty-place behavior when the demand location is false. */
    public function testMapsFalseLocationAsEmptyPlace(): void
    {
        $data = TestHelper::fixture('demand.data.json');

        self::assertEquals(
            Schema::place()->address(Schema::postalAddress()->streetAddress(null)->addressLocality(null)
                ->addressRegion(null)->postalCode(null)->addressCountry(null))->description(null),
            (new MapLocation())->map($data)
        );
    }
}
