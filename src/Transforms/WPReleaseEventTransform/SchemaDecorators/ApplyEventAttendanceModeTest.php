<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyEventAttendanceMode;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOrganizer;
use Spatie\SchemaOrg\Event;

class ApplyEventAttendanceModeTest extends TestCase
{
    private ApplyEventAttendanceMode $decorator;

    protected function setUp(): void
    {
        $this->decorator = new ApplyEventAttendanceMode();
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ApplyEventAttendanceMode::class, $this->decorator);
    }

    #[TestDox('event attendance mode is set to online if event is virtual')]
    public function testEventAttendanceModeIsSetToOnlineIfEventIsVirtual()
    {
        $event = new Event();
        $data  = ['acf' => ['physical_virtual' => 'virtual']];

        $event = $this->decorator->apply($event, $data);

        $this->assertEquals('https://schema.org/OnlineEventAttendanceMode', $event->getProperty('eventAttendanceMode'));
    }

    #[TestDox('event attendance mode defaults to offline if event is not virtual')]
    public function testEventAttendanceModeDefaultsToOfflineIfEventIsNotVirtual()
    {
        $event = new Event();
        $data  = [];

        $event = $this->decorator->apply($event, $data);

        $this->assertEquals('https://schema.org/OfflineEventAttendanceMode', $event->getProperty('eventAttendanceMode'));
    }
}
