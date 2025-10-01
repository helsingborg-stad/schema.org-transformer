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
use DateTime;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('event::schedule is constructed from acf.occasions')]
    public function testItWorks()
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

    #[TestDox('expandWeeklyDates generates correct dates for weekly events')]
    public function testExpandWeeklyDates()
    {
        $events = (new MapEventSchedule())->expandWeeklyDates(
            new DateTime('2025-10-01'),
            new DateTime('2025-10-31'),
            2, // tuesdays
            1  // every week
        );
        $this->assertEquals(
            [
            new DateTime('2025-10-07'),
            new DateTime('2025-10-14'),
            new DateTime('2025-10-21'),
            new DateTime('2025-10-28')
            ],
            $events
        );
    }

    #[TestDox('expandWeeklyDates generates correct dates for weekly events over new years eve')]
    public function testExpandWeeklyDatesOverNewYear()
    {
        $events = (new MapEventSchedule())->expandWeeklyDates(
            new DateTime('2025-12-27'),
            new DateTime('2026-02-07'),
            6, // saturdays
            2  // every 2:nd week
        );
        $this->assertEquals(
            [
            new DateTime('2025-12-27'),
            new DateTime('2026-01-10'),
            new DateTime('2026-01-24'),
            new DateTime('2026-02-07')
            ],
            $events
        );
    }

    #[TestDox('expandWeeklyDates tolerates start and end dates that are not on the correct weekday')]
    public function testExpandWeeklyDatesStartAndEndAreWrongWeekdays()
    {
        $events = (new MapEventSchedule())->expandWeeklyDates(
            new DateTime('2025-10-01'),
            new DateTime('2025-10-31'),
            4, // thursdays
            1  // every week
        );
        $this->assertEquals(
            [
                new DateTime('2025-10-02'),
                new DateTime('2025-10-09'),
                new DateTime('2025-10-16'),
                new DateTime('2025-10-23'),
                new DateTime('2025-10-30')
            ],
            $events
        );
    }
}
