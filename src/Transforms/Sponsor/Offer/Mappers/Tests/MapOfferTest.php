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
    /** Maps offer identity, title, description, and support date. */
    public function testMapsOfferFields(): void
    {
        $result = (new MapOffer())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame('https://example.invalid/offering/exempelerbjudande/', $result['@id']);
        self::assertSame('Exempelerbjudande', $result['name']);
        self::assertSame('2027-09-03T18:05:00+02:00', $result['sponsorshipDate']);
        self::assertNotEmpty($result['description']);
    }

    /** Maps identity and title even when ACF data is absent. */
    public function testMapsTopLevelFieldsWithoutAcf(): void
    {
        $result = (new MapOffer())->map(
            Schema::SponsorOffer(),
            ['guid' => ['rendered' => 'https://example.invalid/?p=42'], 'title' => ['rendered' => 'Offer']],
        )->toArray();

        self::assertSame('https://example.invalid/?p=42', $result['@id']);
        self::assertSame('Offer', $result['name']);
        self::assertArrayNotHasKey('sponsorshipDate', $result);
    }
}
