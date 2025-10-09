<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapPhysicalAccessibilityFeatures::class)]
final class MapPhysicalAccessibilityFeaturesTest extends TestCase
{
    #[TestDox('event::physicalAccessibilityFeatures is constructed from accessibility')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPhysicalAccessibilityFeatures(new WPHeadlessEventTransform('hl')),
            '{
                "accessibility": [
                    "Toalett",
                    "Kiosk"
                ]
            }',
            Schema::event()->physicalAccessibilityFeatures(['Toalett', 'Kiosk'])
        );
    }

    #[TestDox('event::physicalAccessibilityFeatures([]) when accessibility is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPhysicalAccessibilityFeatures(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->physicalAccessibilityFeatures([])
        );
    }
}
