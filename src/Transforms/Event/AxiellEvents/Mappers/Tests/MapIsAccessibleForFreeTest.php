<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapIsAccessibleForFree::class)]
final class MapIsAccessibleForFreeTest extends TestCase
{
    #[TestDox('event::isAccessibleForFree(true) is hardcoded')]
    public function testItsHardcoded()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIsAccessibleForFree(),
            '{
                "id": 123
            }',
            Schema::event()->isAccessibleForFree(true)
        );
    }
}
