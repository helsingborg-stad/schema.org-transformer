<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapStartDate;

#[CoversClass(MapStartDate::class)]
final class MapStartDateTest extends TestCase
{
    #[TestDox('event::startDate is take from all_occasions.start_date if available')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{
                "all_occasions": [
                {
                    "start_date": "2030-02-11 15:15"
                },
                {
                    "start_date": "2040-02-18 15:15"
                },
                {
                    "start_date": "2030-01-02 15:15"
                },
                {
                    "start_date": "2030-03-04 15:15"
                }
                ]
            }',
            Schema::event()->startDate('2030-01-02 15:15')
        );
    }

    #[TestDox('event::startDate(null) when all_occasions is empty or missing')]
    public function testMissingOccasions()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapStartDate(),
            '{"id": 123}',
            Schema::event()->startDate(null)
        );
    }
}
