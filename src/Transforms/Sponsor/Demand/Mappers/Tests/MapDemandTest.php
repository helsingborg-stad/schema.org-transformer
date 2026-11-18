<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapDemand;

#[CoversClass(MapDemand::class)]
final class MapDemandTest extends TestCase
{
    /** Maps source identity, content, and support date. */
    public function testMapsDemandFields(): void
    {
        $result = (new MapDemand())->map(Schema::SponsorDemand(), TestHelper::fixture())->toArray();

        self::assertSame('https://example.invalid/?post_type=assignment&p=1001', $result['@id']);
        self::assertSame('Test namn på aktivitet/sponsringsuppdrag', $result['name']);
        self::assertSame('2026-09-15T12:22:00+02:00', $result['supportNeededBy']);
        self::assertStringContainsString('Test beskrvining', $result['description']);
    }

    /** Keeps top-level fields when ACF fields are absent. */
    public function testMapsTopLevelFieldsWithoutAcf(): void
    {
        $result = (new MapDemand())->map(
            Schema::SponsorDemand(),
            ['guid' => ['rendered' => 'https://example.invalid/?p=42'], 'title' => ['rendered' => 'Demand']],
        )->toArray();

        self::assertSame('https://example.invalid/?p=42', $result['@id']);
        self::assertSame('Demand', $result['name']);
        self::assertArrayNotHasKey('supportNeededBy', $result);
    }
}
