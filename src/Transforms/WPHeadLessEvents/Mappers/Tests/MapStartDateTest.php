<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapStartDate::class)]
final class MapStartDateTest extends TestCase
{
    #[TestDox('event::startDate is constructed from acf.occasions.*.date and acf.occasions.*.startTime')]
    public function testDateAndStartTime()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        { "bad": "data" },
                        {
                            "date": "20230201",
                            "startTime": "12:00:00"
                        },
                        {
                            "date": "20230101",
                            "startTime": "12:34:56"
                        },
                        {
                            "date": "20230301",
                            "startTime": "12:00:00"
                        },
                        {
                            "date": "20230401",
                            "startTime": "12:00:00"
                        }
                    ]
                }
            }',
            Schema::event()->startDate('2023-01-01T12:34:56+00:00')
        );
    }

    #[TestDox('event::startDate is constructed from acf.occasions.*.date when no startTime is given')]
    public function testDatesOnly()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "occasions": [
                        { "bad": "data" },
                        {
                            "date": "20230201"
                        },
                        {
                            "date": "20230101"
                        },
                        {
                            "date": "20230301"
                        },
                        {
                            "date": "20230401"
                        }
                    ]
                }
            }',
            Schema::event()->startDate('2023-01-01T00:00:00+00:00')
        );
    }

    #[TestDox('event::startDate(null) when acf.occasions is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->startDate(null)
        );
    }
}
