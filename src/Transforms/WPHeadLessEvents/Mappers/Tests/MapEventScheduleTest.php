<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('event::schedule is mapped from weekly acf.occasions for single occasions')]
    public function testSingleScheduleMapping()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "repeat": "no",
                            "date": "20251001",
                            "untilDate": "20251001",
                            "startTime": "12:00:00",
                            "endTime": "15:00:00",
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2025-10-01')
                    ->endDate('2025-10-01')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
            ])
        );
    }

    #[TestDox('event::schedule is expanded from monthly acf.occasions')]
    public function testMonthlyScheduleMapping()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "repeat": "byMonth",
                            "monthsInterval": 2,
                            "monthDayNumber": 3,
                            "date": "20251101",
                            "untilDate": "20261130",
                            "startTime": "12:00:00",
                            "endTime": "15:00:00",
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2025-11-03')
                    ->endDate('2025-11-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-01-03')
                    ->endDate('2026-01-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-03-03')
                    ->endDate('2026-03-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-05-03')
                    ->endDate('2026-05-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-07-03')
                    ->endDate('2026-07-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-09-03')
                    ->endDate('2026-09-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2026-11-03')
                    ->endDate('2026-11-03')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00')
            ])
        );
    }

    #[TestDox('event::schedule is expanded from weekly acf.occasions')]
    public function testWeeklyScheduleMapping()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "repeat": "byWeek",
                            "weeksInterval": 2,
                            "monthsInterval": 1,
                            "weekDays": [
                                "https://schema.org/Wednesday",
                                "https://schema.org/Thursday"
                            ],
                            "monthDay": "dag",
                            "monthDayNumber": 1,
                            "date": "20251001",
                            "untilDate": "20251031",
                            "startTime": "12:00:00",
                            "endTime": "15:00:00",
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2025-10-01')
                    ->endDate('2025-10-01')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-02')
                    ->endDate('2025-10-02')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-15')
                    ->endDate('2025-10-15')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-16')
                    ->endDate('2025-10-16')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-29')
                    ->endDate('2025-10-29')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-30')
                    ->endDate('2025-10-30')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00')

            ])
        );
    }
}
