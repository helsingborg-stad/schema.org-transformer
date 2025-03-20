<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;

class ApplyEventSeriesTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(ApplyEventSeries::class, new ApplyEventSeries());
    }

    #[TestDox('creates event series with event using originalId')]
    public function testCreatesEventSeriesWithEventUsingOriginalId()
    {
        $event = Schema::event();

        $data = ['eventsInSameSeries' => ['123', '456']];

        $event = (new ApplyEventSeries())->apply($event, $data);

        $this->assertEquals(['123', '456'], $event->getProperty('eventsInSameSeries'));
    }
}
