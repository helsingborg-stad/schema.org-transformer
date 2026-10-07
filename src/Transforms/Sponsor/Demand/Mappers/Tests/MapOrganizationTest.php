<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapOrganization;

#[CoversClass(MapOrganization::class)]
final class MapOrganizationTest extends TestCase
{
    /** Maps organization fields, website, and contact point. */
    public function testMapsOrganization(): void
    {
        $result = (new MapOrganization())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertSame('Exempelföreningen', $result['organization']['name']);
        self::assertSame('https://example.invalid', $result['organization']['url']);
        self::assertSame('0000000000', $result['organization']['taxID']);
        self::assertSame('kontakt@example.invalid', $result['organization']['contactPoint']['email']);
    }

    /** Leaves the event unchanged when ACF data is absent. */
    public function testKeepsEventWithoutAcf(): void
    {
        $result = (new MapOrganization())->map(Schema::SponsorDemandEvent(), ['id' => 42])->toArray();

        self::assertArrayNotHasKey('organization', $result);
    }
}
