<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventStatus;

#[CoversClass(MapEventStatus::class)]
final class MapEventStatusTest extends TestCase
{
    #[TestDox('event::eventStatus is taken from first all_occasion status, scheduled')]
    public function testEventStatusScheduled()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventStatus(),
            '{
                "all_occasions": [
                {
                    "status": "scheduled"
                },
                {
                    "status": "rescheduled"
                }
                ]
            }',
            Schema::event()->eventStatus(Schema::eventStatusType()::EventScheduled)
        );
    }

    #[TestDox('event::eventStatus is taken from first all_occasion status, rescheduled')]
    public function testEventStatusRescheduled()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventStatus(),
            '{
                "all_occasions": [
                {
                    "status": "rescheduled"
                },
                {
                    "status": "scheduled"
                }
                ]
            }',
            Schema::event()->eventStatus(Schema::eventStatusType()::EventRescheduled)
        );
    }

    #[TestDox('event::eventStatus is taken from first all_occasion status, cancelled')]
    public function testEventStatusCancelled()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventStatus(),
            '{
                "all_occasions": [
                {
                    "status": "cancelled"
                },
                {
                    "status": "scheduled"
                }
                ]
            }',
            Schema::event()->eventStatus(Schema::eventStatusType()::EventCancelled)
        );
    }
}
