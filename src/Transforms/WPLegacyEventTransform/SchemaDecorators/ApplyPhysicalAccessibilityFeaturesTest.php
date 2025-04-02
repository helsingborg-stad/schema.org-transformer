<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

class ApplyPhysicalAccessibilityFeaturesTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ApplyPhysicalAccessibilityFeatures::class, new ApplyPhysicalAccessibilityFeatures());
    }

    #[TestDox('applies physical accessibility features')]
    public function testAppliesPhysicalAccessibilityFeatures(): void
    {
        $event = Schema::event();
        $data  = ["accessibility" => ["Accessible toilet","Elevator/ramp"]];

        $decorator = new ApplyPhysicalAccessibilityFeatures();
        $result    = $decorator->apply($event, $data);

        $this->assertCount(2, $result->getProperty('physicalAccessibilityFeatures'));
    }

    #[TestDox('maps accessibility term names')]
    public function testMapsAccessibilityTermNames(): void
    {
        $event = Schema::event();
        $data  = ["accessibility" => ["Accessible toilet","Elevator/ramp"]];

        $decorator = new ApplyPhysicalAccessibilityFeatures();
        $result    = $decorator->apply($event, $data);

        $this->assertEquals(['Handikapptoalett', 'Hiss/ramp'], $result->getProperty('physicalAccessibilityFeatures'));
    }
}
