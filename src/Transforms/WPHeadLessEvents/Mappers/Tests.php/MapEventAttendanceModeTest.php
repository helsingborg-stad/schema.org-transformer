<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapEventAttendanceMode::class)]
final class MapEventAttendanceModeTest extends TestCase
{
    #[TestDox('event::eventAttendanceMode(OnlineEventAttendanceMode) when acf.attendancemode = online')]
    public function testOnline()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "attendancemode": "online"
                }
            }',
            Schema::event()->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::OnlineEventAttendanceMode)
        );
    }

    #[TestDox('event::eventAttendanceMode(OfflineEventAttendanceMode) when acf.attendancemode = offline')]
    public function testOffline()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "acf": {
                    "attendancemode": "offline"
                }
            }',
            Schema::event()->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode)
        );
    }

    #[TestDox('event::eventAttendanceMode(MixedEventAttendanceMode) when acf.attendancemode = mixed')]
    public function testMixed()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "acf": {
                    "attendancemode": "mixed"
                }
            }',
            Schema::event()->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::MixedEventAttendanceMode)
        );
    }

    #[TestDox('event::eventAttendanceMode(OfflineEventAttendanceMode) when acf . attendancemode is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEventAttendanceMode(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode)
        );
    }
}
