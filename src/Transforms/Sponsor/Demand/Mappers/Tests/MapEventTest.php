<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapEvent;

#[CoversClass(MapEvent::class)]
final class MapEventTest extends TestCase
{
    /** Maps source identity, content, and event start date. */
    public function testMapsEventFields(): void
    {
        $result = (new MapEvent())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertSame(1001, $result['@id']);
        self::assertSame('Test namn på aktivitet/sponsringsuppdrag', $result['name']);
        self::assertSame('2026-09-15T12:22:00', $result['startDate']);
        self::assertStringContainsString('Test beskrvining', $result['description']);
    }

    /** Keeps top-level fields when ACF fields are absent. */
    public function testMapsTopLevelFieldsWithoutAcf(): void
    {
        $result = (new MapEvent())->map(
            Schema::SponsorDemandEvent(),
            ['id' => 42, 'title' => ['rendered' => 'Demand']],
        )->toArray();

        self::assertSame(42, $result['@id']);
        self::assertSame('Demand', $result['name']);
        self::assertArrayNotHasKey('startDate', $result);
    }
}
