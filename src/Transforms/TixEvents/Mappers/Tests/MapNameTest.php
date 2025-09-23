<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapName;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('event::name is set from source->Name')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "Name": "Event name from source"
            }',
            Schema::event()
                ->name("Event name from source")
        );
    }
}
