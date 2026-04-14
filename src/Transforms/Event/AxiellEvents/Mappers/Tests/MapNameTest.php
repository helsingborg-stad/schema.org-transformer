<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('event::name is taken from $.title')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "title": "Some Event Name"
            }',
            Schema::event()->name('Some Event Name')
        );
    }

    #[TestDox('event::name(null) when $.title is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "id": 123
            }',
            Schema::event()->name(null)
        );
    }
}
