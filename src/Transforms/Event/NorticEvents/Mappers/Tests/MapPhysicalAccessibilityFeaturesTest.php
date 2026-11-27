<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapPhysicalAccessibilityFeatures;

#[CoversClass(MapPhysicalAccessibilityFeatures::class)]
final class MapPhysicalAccessibilityFeaturesTest extends TestCase
{
    #[TestDox('event::physicalAccessibilityFeatures is empty when Nortic has no mapping')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapPhysicalAccessibilityFeatures(),
            '{}',
            Schema::event()->physicalAccessibilityFeatures([])
        );
    }
}
