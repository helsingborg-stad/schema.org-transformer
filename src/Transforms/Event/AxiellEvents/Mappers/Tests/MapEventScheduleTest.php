<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEventSchedule::class)]
final class MapEventScheduleTest extends TestCase
{
    #[TestDox('event::eventSchedule is constructed from $.startDate and $.endDate')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{
                "startDate": "2024-09-01T19:00:00",
                "endDate": "2024-09-01T21:00:00"
            }',
            Schema::event()->eventSchedule([
                Schema::schedule()
                    ->startDate('2024-09-01T19:00:00')
                    ->endDate('2024-09-01T21:00:00')
            ])
        );
    }

    #[TestDox('event::eventSchedule([]) when $.startDate or $.endDate is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventSchedule(),
            '{
                "id": 123
            }',
            Schema::event()->eventSchedule([])
        );
    }
}
