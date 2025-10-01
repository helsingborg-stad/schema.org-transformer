<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEndDate::class)]
final class MapEndDateTest extends TestCase
{
    #[TestDox('event::endDate is constructed from acf.occasions.*.untilDate and acf.occasions.*.endTime')]
    public function testDateAndEndTime()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        { "bad": "data" },
                        {
                            "untilDate": "20230201",
                            "endTime": "12:00:00"
                        },
                        {
                            "untilDate": "20230601",
                            "endTime": "12:34:56"
                        },
                        {
                            "untilDate": "20230301",
                            "endTime": "12:00:00"
                        },
                        {
                            "untilDate": "20230401",
                            "endTime": "12:00:00"
                        }
                    ]
                }
            }',
            Schema::event()->endDate('2023-06-01T12:34:56+00:00')
        );
    }

    #[TestDox('event::endDate is constructed from acf.occasions.*.untilDate when no endTime is given')]
    public function testDatesOnly()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        { "bad": "data" },
                        {
                            "untilDate": "20230201"
                        },
                        {
                            "untilDate": "20230602"
                        },
                        {
                            "untilDate": "20230301"
                        },
                        {
                            "untilDate": "20230401"
                        }
                    ]
                }
            }',
            Schema::event()->endDate('2023-06-02T00:00:00+00:00')
        );
    }

    #[TestDox('event::endDate(null) when acf.occasions is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->endDate(null)
        );
    }
}
