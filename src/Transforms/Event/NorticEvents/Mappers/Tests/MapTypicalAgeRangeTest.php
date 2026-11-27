<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapTypicalAgeRange;

#[CoversClass(MapTypicalAgeRange::class)]
final class MapTypicalAgeRangeTest extends TestCase
{
    #[TestDox('event::typicalAgeRange is null when Nortic has no mapping')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTypicalAgeRange(),
            '{}',
            Schema::event()->typicalAgeRange(null)
        );
    }
}
