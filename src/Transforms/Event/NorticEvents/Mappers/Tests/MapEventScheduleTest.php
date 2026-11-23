<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventSchedule;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('event::eventSchedule is mapped from source shows')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{"shows":[{"startDate":"2026-11-27 19:00","playTimeInMinutes":60,"link":"https://example.com/show/1"}]}',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2026-11-27 19:00')
                    ->endDate('2026-11-27 20:00')
                    ->url('https://example.com/show/1')
            ])
        );
    }

    #[TestDox('event::eventSchedule is empty when there are no source shows')]
    public function testNoShows(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{"shows":[]}',
            Schema::event()->eventSchedule([])
        );
    }
}
