<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapDemandEvent;

#[CoversClass(MapDemandEvent::class)]
final class MapDemandEventTest extends TestCase
{
    /** Verify that the demand event retains its identifier, content, and local start time. */
    public function testMapsDemandEvent(): void
    {
        $data = TestHelper::fixture('demand.data.json');

        $event = (new MapDemandEvent())->map($data);

        self::assertEquals(
            Schema::sponsorDemandEvent()
                ->identifier(1001)
                ->name('Test namn på aktivitet/sponsringsuppdrag')
                ->description("<p>Test beskrvining av aktiviteten/sponsringsuppdraget</p>\n")
                ->startDate('2026-09-15T12:22:00'),
            $event
        );
    }

    /** Verify that no event is mapped without ACF fields. */
    public function testReturnsNullWithoutAcf(): void
    {
        $data        = TestHelper::fixture('demand.data.json');
        $data['acf'] = null;

        self::assertNull((new MapDemandEvent())->map($data));
    }
}
