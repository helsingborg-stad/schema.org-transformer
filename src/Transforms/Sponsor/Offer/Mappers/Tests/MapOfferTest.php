<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapOffer;

#[CoversClass(MapOffer::class)]
final class MapOfferTest extends TestCase
{
    /** Maps offer identity, title, end date, and resource categories. */
    public function testMapsOfferFields(): void
    {
        $result = (new MapOffer())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame(1002, $result['@id']);
        self::assertSame('Exempelerbjudande', $result['name']);
        self::assertSame('2027-02-05T19:04:00', $result['availabilityEnds']);
        self::assertSame(['stöd och hjälp', 'pengar', 'mat- och dryck', 'logistik', 'övrigt'], $result['category']);
    }

    /** Maps identity and title even when ACF data is absent. */
    public function testMapsTopLevelFieldsWithoutAcf(): void
    {
        $result = (new MapOffer())->map(
            Schema::SponsorOffer(),
            ['id' => 42, 'title' => ['rendered' => 'Offer']],
        )->toArray();

        self::assertSame(42, $result['@id']);
        self::assertSame('Offer', $result['name']);
        self::assertSame([], $result['category']);
    }
}
