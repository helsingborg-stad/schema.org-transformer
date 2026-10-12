<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapLocation;

#[CoversClass(MapLocation::class)]
final class MapLocationTest extends TestCase
{
    /** Maps the ACF location fields to a place and postal address. */
    public function testMapsLocation(): void
    {
        $data                    = TestHelper::fixture();
        $data['acf']['location'] = [
            'address'   => 'Example street 1',
            'name'      => 'Example venue',
            'city'      => 'Example city',
            'state'     => 'Example county',
            'post_code' => '12345',
            'country'   => 'Sweden',
        ];

        $result = (new MapLocation())->map(Schema::SponsorDemandEvent(), $data)->toArray();

        self::assertSame('Example venue', $result['location']['address']['streetAddress']);
        self::assertSame('Example city', $result['location']['address']['addressLocality']);
        self::assertSame('12345', $result['location']['address']['postalCode']);
        self::assertSame('Example street 1', $result['location']['description']);
    }

    /** Does not create an empty place for a false ACF value. */
    public function testSkipsFalseLocationField(): void
    {
        $result = (new MapLocation())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertArrayNotHasKey('location', $result);
    }
}
