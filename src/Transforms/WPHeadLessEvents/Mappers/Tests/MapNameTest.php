<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapName;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('event::name is constructed from acf.name')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "name": "Test event"
                }
            }',
            Schema::event()->name('Test event')
        );
    }

    #[TestDox('event::name(null) when acf.name is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->name(null)
        );
    }
}
