<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEventAttendanceMode::class)]
final class MapEventAttendanceModeTest extends TestCase
{
    #[TestDox('event::attendanceMode(https://schema.org/OfflineEventAttendanceMode) hardcoded')]
    public function testItsHardcoded()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(),
            '{
                "id": 123
            }',
            Schema::event()->eventAttendanceMode('https://schema.org/OfflineEventAttendanceMode')
        );
    }
}
