<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('event::description is constructed from $.description')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "description": "Test event description"
            }',
            Schema::event()->description(['Test event description'])
        );
    }

    #[TestDox('event::description([]) when $.description is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "id": 123
            }',
            Schema::event()->description([])
        );
    }
}
