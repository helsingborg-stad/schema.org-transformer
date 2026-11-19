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
        $result = (new MapOrganization())->map(Schema::SponsorDemand()->setProperty('@id', 'https://example.invalid/?p=42'), TestHelper::fixture())->toArray();

        self::assertSame('Exempelföreningen', $result['@reverse']['seeks']['name']);
        self::assertSame('https://example.invalid', $result['@reverse']['seeks']['url']);
        self::assertSame('bollsport', $result['@reverse']['seeks']['keywords'][0]['name']);
        self::assertSame('kontakt@example.invalid', $result['@reverse']['seeks']['contactPoint']['email']);
    }

    /** Leaves the demand unchanged when ACF data is absent. */
    public function testKeepsDemandWithoutAcf(): void
    {
        $result = (new MapOrganization())->map(Schema::SponsorDemand(), ['id' => 42])->toArray();

        self::assertArrayNotHasKey('@reverse', $result);
    }
}
