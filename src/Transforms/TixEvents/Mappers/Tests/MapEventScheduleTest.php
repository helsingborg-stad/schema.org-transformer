<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\TixEvents\TixEventTransform;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('description is set from source->Dates where EventId and DefaultEventGroupId matches')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(new TixEventTransform('tix_')),
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
                ->eventSchedule([
                    Schema::schedule()
                        ->identifier('tix_123_1')
                        ->startDate('2024-10-01T18:00:00+02:00')
                        ->endDate('2024-10-01T20:00:00+02:00'),
                    Schema::schedule()
                        ->identifier('tix_123_10')
                        ->startDate('2024-10-15T18:00:00+02:00')
                        ->endDate('2024-10-15T20:00:00+02:00')
                ])
        );
    }
}
