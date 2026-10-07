<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    /** Maps offering activities to defined terms in source order. */
    public function testMapsActivities(): void
    {
        $result = (new MapKeywords())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame(['cykling', 'e-sport'], array_column($result['keywords'], 'name'));
        self::assertSame('activity', $result['keywords'][0]['inDefinedTermSet']['name']);
    }

    /** Maps empty activities to an empty keyword list. */
    public function testMapsEmptyActivities(): void
    {
        $data                      = TestHelper::fixture('offering.data.json');
        $data['acf']['activities'] = [];

        $result = (new MapKeywords())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertSame([], $result['keywords'] ?? []);
    }

    /** Leaves the offer unchanged when the activities field is false. */
    public function testSkipsFalseActivitiesField(): void
    {
        $data                      = TestHelper::fixture('offering.data.json');
        $data['acf']['activities'] = false;

        $result = (new MapKeywords())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertArrayNotHasKey('keywords', $result);
    }
}
