<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventSchedule;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('event::eventSchedule([]) when occasions is empty or missing')]
    public function testEmptyOccasions()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{
                "all_occasions": []
            }',
            Schema::event()
            ->eventSchedule([])
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{
                "id": 123
            }',
            Schema::event()
            ->eventSchedule([])
        );
    }

    #[TestDox('event::eventSchedule is taken from source-all_occasions')]
    public function testMapEventSchedule()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{
                "all_occasions": [
                {
                    "start_date": "2030-02-11 15:15",
                    "end_date": "2030-02-11 16:30",
                    "door_time": null,
                    "status": "scheduled",
                    "occ_exeption_information": null,
                    "content_mode": null,
                    "content": null,
                    "location_mode": null,
                    "location": null
                },
                {
                    "start_date": "2040-02-18 15:15",
                    "end_date": "2040-02-18 16:30",
                    "door_time": null,
                    "status": "rescheduled",
                    "occ_exeption_information": null,
                    "content_mode": null,
                    "content": null,
                    "location_mode": null,
                    "location": null
                },
                {
                    "start_date": "2030-03-04 15:15",
                    "end_date": "2030-03-04 16:30",
                    "door_time": null,
                    "status": "cancelled",
                    "occ_exeption_information": null,
                    "content_mode": null,
                    "content": null,
                    "location_mode": null,
                    "location": null
                }
                ]
            }',
            Schema::event()
                ->eventSchedule([
                    Schema::schedule()
                        ->startDate('2030-02-11 15:15')
                        ->endDate('2030-02-11 16:30')
                        ->url(null),
                    Schema::schedule()
                        ->startDate('2040-02-18 15:15')
                        ->endDate('2040-02-18 16:30')
                        ->url(null),
                    Schema::schedule()
                        ->startDate('2030-03-04 15:15')
                        ->endDate('2030-03-04 16:30')
                        ->url(null),
                ])
        );
    }
}
