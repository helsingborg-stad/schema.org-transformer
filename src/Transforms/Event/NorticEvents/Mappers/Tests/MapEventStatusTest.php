<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventStatus;

#[CoversClass(MapEventStatus::class)]
final class MapEventStatusTest extends TestCase
{
    #[TestDox('event::eventStatus is scheduled')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventStatus(),
            '{}',
            Schema::event()->eventStatus(Schema::eventStatusType()::EventScheduled)
        );
    }
}
