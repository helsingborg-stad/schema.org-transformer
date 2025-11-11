<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEndDate;

#[CoversClass(MapEndDate::class)]
final class MapEndDateTest extends TestCase
{
    #[TestDox('event::endDate is taken from source.all_occasions.end_date if available')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{
                "all_occasions": [
                    {
                        "end_date": "2030-02-11 16:30"
                    },
                    {
                        "end_date": "2040-02-18 16:30"
                    },
                    {
                        "end_date": "2030-03-04 16:30"
                    }
                ]
            }',
            Schema::event()->endDate('2040-02-18 16:30')
        );
    }

    #[TestDox('event::endDate(null) when all_occasions is empty or missing')]
    public function testMissingOccasions()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{"id": 123}',
            Schema::event()->endDate(null)
        );
    }
}
