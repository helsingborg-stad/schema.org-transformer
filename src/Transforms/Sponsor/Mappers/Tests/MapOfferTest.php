<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapOffer;

#[CoversClass(MapOffer::class)]
final class MapOfferTest extends TestCase
{
    /** Verify that the offering deadline and resource categories are mapped. */
    public function testMapsOffering(): void
    {
        $data = TestHelper::fixture('offering.data.json');

        $offer = (new MapOffer())->map($data);

        self::assertEquals(
            Schema::sponsorOffer()
                ->availabilityEnds('2027-02-05T19:04:00')
                ->category(['stöd och hjälp', 'pengar', 'mat- och dryck', 'logistik', 'övrigt']),
            $offer
        );
    }

    /** Verify that no offer is mapped without ACF fields. */
    public function testReturnsNullWithoutAcf(): void
    {
        $data        = TestHelper::fixture('offering.data.json');
        $data['acf'] = null;

        self::assertNull((new MapOffer())->map($data));
    }
}
