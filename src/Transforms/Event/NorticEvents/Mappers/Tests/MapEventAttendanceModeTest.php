<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventAttendanceMode;

#[CoversClass(MapEventAttendanceMode::class)]
final class MapEventAttendanceModeTest extends TestCase
{
    #[TestDox('event::eventAttendanceMode is offline')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(),
            '{}',
            Schema::event()->eventAttendanceMode('https://schema.org/OfflineEventAttendanceMode')
        );
    }
}
