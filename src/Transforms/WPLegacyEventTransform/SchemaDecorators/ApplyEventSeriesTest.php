<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

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

        $data = ['eventsInSameSeries' => ['123','456']];

        $event = (new ApplyEventSeries())->apply($event, $data);

        $this->assertEquals('123', $event->getProperty('eventsInSameSeries')[0]->getProperty('identifier'));
        $this->assertEquals('456', $event->getProperty('eventsInSameSeries')[1]->getProperty('identifier'));
    }
}
