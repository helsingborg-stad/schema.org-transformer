<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('event::name is constructed from title.rendered')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "title": {
                    "rendered": "Test event"
                }
            }',
            Schema::event()->name('Test event')
        );
    }

    #[TestDox('event::name(null) when title.rendered is missing')]
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
