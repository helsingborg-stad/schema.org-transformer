<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapSponsorshipOffer;

#[CoversClass(MapSponsorshipOffer::class)]
final class MapSponsorshipOfferTest extends TestCase
{
    /** Maps due date, resource categories, and demand requirements. */
    public function testMapsSponsorshipOffer(): void
    {
        $result = (new MapSponsorshipOffer())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertSame('2026-11-11T16:46:24', $result['hasSponsorshipOffer']['availabilityEnds']);
        self::assertSame(['ekonomi'], $result['hasSponsorshipOffer']['category']);
        self::assertSame('Test krav', $result['hasSponsorshipOffer']['demand']['description']);
    }

    /** Does not add a sponsorship offer when ACF data is absent. */
    public function testSkipsSponsorshipOfferWithoutAcf(): void
    {
        $result = (new MapSponsorshipOffer())->map(Schema::SponsorDemandEvent(), ['id' => 42])->toArray();

        self::assertArrayNotHasKey('hasSponsorshipOffer', $result);
    }
}
