<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapOfferedBy;

#[CoversClass(MapOfferedBy::class)]
final class MapOfferedByTest extends TestCase
{
    /** Maps the offering organization and its contact, demand, and keyword. */
    public function testMapsOfferedByOrganization(): void
    {
        $result = (new MapOfferedBy())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame('Exempelföreningen', $result['offeredBy']['name']);
        self::assertSame('https://example.invalid', $result['offeredBy']['url']);
        self::assertSame('kontakt@example.invalid', $result['offeredBy']['contactPoint']['email']);
        self::assertSame(
            'Deleo temptatio verus valetudo theatrum a aufero.',
            $result['offeredBy']['demand']['description'],
        );
        self::assertSame(
            'Vapulus vulgo barba aer censura cui ipsa stabilis substantia fugiat.',
            $result['offeredBy']['keywords'][0]['name'],
        );
    }

    /** Leaves the offer unchanged when ACF data is absent. */
    public function testSkipsOfferedByWithoutAcf(): void
    {
        $result = (new MapOfferedBy())->map(Schema::SponsorOffer(), ['id' => 42])->toArray();

        self::assertArrayNotHasKey('offeredBy', $result);
    }
}
