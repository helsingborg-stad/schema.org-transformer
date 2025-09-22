<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('event::description is set from source->SubTitle')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "SubTitle": "Event description from source"
            }',
            Schema::event()
                ->description("Event description from source")
        );
    }
}
