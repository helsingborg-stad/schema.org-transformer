<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventAttendanceMode;

#[CoversClass(MapEventAttendanceMode::class)]
final class MapEventAttendanceModeTest extends TestCase
{
    #[TestDox('event::eventAttendanceMode is hardcoded to OfflineEventAttendanceMode')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(),
            '{
                "id": 123
            }',
            Schema::event()->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode)
        );
    }
}
