<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEventStatus::class)]
final class MapEventStatusTest extends TestCase
{
    #[TestDox('event::eventStatus(Scheduled) is hardcoded')]
    public function testItsHardcoded()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventStatus(),
            '{
                "id": 123
            }',
            Schema::event()->eventStatus(Schema::eventStatusType()::EventScheduled)
        );
    }
}
