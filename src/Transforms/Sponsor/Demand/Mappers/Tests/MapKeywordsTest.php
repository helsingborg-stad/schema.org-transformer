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
    /** Omits keywords when resources are absent. */
    public function testOmitsKeywordsWhenResourcesAreMissing(): void
    {
        $result = (new MapKeywords())->map(Schema::SponsorDemand(), [])->toArray();

        self::assertSame([], $result['keywords'] ?? []);
    }

    /** Maps resources to defined terms. */
    public function testMapsResources(): void
    {
        $result = (new MapKeywords())->map(Schema::SponsorDemand(), TestHelper::fixture())->toArray();

        self::assertSame('ekonomi', $result['keywords'][0]['name']);
        self::assertSame(
            'resource',
            $result['keywords'][0]['inDefinedTermSet']['name'],
        );
    }
}
