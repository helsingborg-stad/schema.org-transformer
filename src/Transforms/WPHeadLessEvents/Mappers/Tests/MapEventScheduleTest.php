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
                    ->startDate('2025-10-01 12:00:00')
                    ->endDate('2025-10-01 15:00:00')
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
                    ->startDate('2025-10-01 12:00:00')
                    ->endDate('2025-10-01 15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-02 12:00:00')
                    ->endDate('2025-10-02 15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-15 12:00:00')
                    ->endDate('2025-10-15 15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-16 12:00:00')
                    ->endDate('2025-10-16 15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-29 12:00:00')
                    ->endDate('2025-10-29 15:00:00'),
                Schema::schedule()
                    ->startDate('2025-10-30 12:00:00')
                    ->endDate('2025-10-30 15:00:00')
            ])
        );
    }

    #[TestDox('does not fail when startTime or endTime is missing')]
    public function testMissingTimeHandling()
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
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate(null)
                    ->endDate(null)
            ])
        );
    }

    #[TestDox('does not fail when startDate or endDate is missing')]
    public function testMissingDateHandling()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        {
                            "repeat": "no",
                            "startTime": "12:00:00",
                            "endTime": "15:00:00",
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate(null)
                    ->endDate(null)
            ])
        );
    }
}
