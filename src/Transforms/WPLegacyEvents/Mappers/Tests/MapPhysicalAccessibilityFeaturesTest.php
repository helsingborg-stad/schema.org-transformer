<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapPhysicalAccessibilityFeatures;

#[CoversClass(MapPhysicalAccessibilityFeatures::class)]
final class MapPhysicalAccessibilityFeaturesTest extends TestCase
{
    #[TestDox('event::physicalAccessibilityFeatures is mapped and translated from accessibility terms')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPhysicalAccessibilityFeatures(),
            '{
                "accessibility": [
                    "Accessible toilet",
                    "Elevator/ramp",
                    "Some other feature that isnt translated"
                ]
            }',
            Schema::event()->physicalAccessibilityFeatures([
                "Handikapptoalett",
                "Hiss/ramp",
                "Some other feature that isnt translated"
                ])
        );
    }

    #[TestDox('event::physicalAccessibilityFeatures([]) when no features are present')]
    public function testHandlesMissingFeatures()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPhysicalAccessibilityFeatures(),
            '{"id": 123}',
            Schema::event()->physicalAccessibilityFeatures([])
        );
    }
}
