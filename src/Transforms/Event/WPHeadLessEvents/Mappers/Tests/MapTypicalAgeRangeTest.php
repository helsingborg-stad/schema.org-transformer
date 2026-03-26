<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapTypicalAgeRange;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapTypicalAgeRange::class)]
final class MapTypicalAgeRangeTest extends TestCase
{
    #[TestDox('event::typicalAgeRange([]) always')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->typicalAgeRange(null)
        );
    }
}
