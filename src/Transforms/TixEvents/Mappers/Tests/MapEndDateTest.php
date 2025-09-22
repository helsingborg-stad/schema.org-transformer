<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapEndDate;

#[CoversClass(MapEndDate::class)]
final class MapEndDateTest extends TestCase
{
    #[TestDox('event::endDate is taken from source->Dates->EndDate')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{
            "EventGroupId": 123,
            "Dates": [
                {
                    "EventId": 1,
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-01T18:00:00+02:00",
                    "EndDate": "2024-10-01T20:00:00+02:00"
                },
                {
                    "NOTE": "This event should be ignored since it belongs to another group",
                    "EventId": 2,
                    "DefaultEventGroupId": 555,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                },
                {
                    "NOTE": "This event should be ignored since it has no EventId",
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                },
                {
                    "EventId": 10,
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                }
            ]
        }',
            Schema::event()
                ->endDate('2024-10-15T20:00:00+02:00')
        );
    }

    #[TestDox('event::endDate is not set if no source-Dates->EndDate exists')]
    public function testNoDates()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEndDate(),
            '{
                "EventGroupId": 123
            }',
            Schema::event()
        );
    }
}
