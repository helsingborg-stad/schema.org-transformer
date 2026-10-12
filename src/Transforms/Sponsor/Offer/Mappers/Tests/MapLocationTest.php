<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    /** Maps location and postal address fields. */
    public function testMapsLocation(): void
    {
        $result = (new MapLocation())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame('Exempelstad', $result['location']['address']['addressLocality']);
        self::assertSame('123 45', $result['location']['address']['postalCode']);
        self::assertSame('Exempelvägen 1, 123 45 Exempelstad, Sverige', $result['location']['description']);
    }

    /** Skips a false location field. */
    public function testSkipsFalseLocationField(): void
    {
        $data                    = TestHelper::fixture('offering.data.json');
        $data['acf']['location'] = false;

        $result = (new MapLocation())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertArrayNotHasKey('location', $result);
    }
}
