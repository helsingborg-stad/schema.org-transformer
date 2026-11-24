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
    /** Maps offering resources to defined terms in source order. */
    public function testMapsResources(): void
    {
        $result = (new MapKeywords())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame(['stöd och hjälp', 'pengar', 'mat- och dryck', 'logistik', 'övrigt'], array_column($result['keywords'], 'name'));
        self::assertSame('resource', $result['keywords'][0]['inDefinedTermSet']['name']);
    }

    /** Maps empty resources to an empty keyword list. */
    public function testMapsEmptyResources(): void
    {
        $data                     = TestHelper::fixture('offering.data.json');
        $data['acf']['resources'] = [];

        $result = (new MapKeywords())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertSame([], $result['keywords'] ?? []);
    }

    /** Leaves the offer unchanged when the resources field is false. */
    public function testSkipsFalseResourcesField(): void
    {
        $data                     = TestHelper::fixture('offering.data.json');
        $data['acf']['resources'] = false;

        $result = (new MapKeywords())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertArrayNotHasKey('keywords', $result);
    }
}
