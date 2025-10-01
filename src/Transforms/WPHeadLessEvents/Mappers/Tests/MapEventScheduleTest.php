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
                            "weeksInterval": 3,
                            "monthsInterval": 1,
                            "weekDays": [
                                "https://schema.org/Monday",
                                "https://schema.org/Wednesday"
                            ],
                            "monthDay": "dag",
                            "monthDayNumber": 1,
                            "date": "20250818",
                            "untilDate": "20251114",
                            "startTime": "12:00:00",
                            "endTime": "15:00:00",
                            "url": ""
                        }
                    ]
                }
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2025-08-18')
                    ->endDate('2025-11-14')
                    ->startTime('12:00:00')
                    ->endTime('15:00:00')
                    ->byDay([
                        'https://schema.org/Monday',
                        'https://schema.org/Wednesday',
                    ])
            ])
        );
    }

    #[TestDox('event::name(null) when acf.name is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->eventSchedule([])
        );
    }
}
