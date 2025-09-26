<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapIsAccessibleForFree;

#[CoversClass(MapIsAccessibleForFree::class)]
final class MapIsAccessibleForFreeTest extends TestCase
{
    #[TestDox('event::isAccessibleForFree is set from source->Dates->IsFreeEvent')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "EventGroupId": 123,
                "Dates": [
                    {
                        "EventId": 1,
                        "DefaultEventGroupId": 123,
                        "IsFreeEvent": true
                    },
                    {
                        "EventId": 2,
                        "DefaultEventGroupId": 123,
                        "IsFreeEvent": false
                    }
                ]
            }',
            Schema::event()
                ->isAccessibleForFree(true)
        );
    }

    public function testDefaultsToFalse()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "EventGroupId": 123
            }',
            Schema::event()
                ->isAccessibleForFree(false)
        );
    }
}
