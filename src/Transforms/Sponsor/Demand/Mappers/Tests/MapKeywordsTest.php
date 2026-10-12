<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapKeywords;

#[CoversClass(MapKeywords::class)]
final class MapKeywordsTest extends TestCase
{
    /** Omits the grant keyword when eligibility is false. */
    public function testOmitsKeywordWhenNotEligible(): void
    {
        $result = (new MapKeywords())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertSame([], $result['keywords'] ?? []);
    }

    /** Maps true grant eligibility to a textual defined term. */
    public function testMapsEligibleGrantKeyword(): void
    {
        $data                                            = TestHelper::fixture();
        $data['acf']['organization_eligible_for_grants'] = true;

        $result = (new MapKeywords())->map(Schema::SponsorDemandEvent(), $data)->toArray();

        self::assertSame('eligible', $result['keywords'][0]['name']);
        self::assertSame(
            'organization_eligible_for_grants',
            $result['keywords'][0]['inDefinedTermSet']['name'],
        );
    }
}
