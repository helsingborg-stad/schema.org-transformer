<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapName;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('event::name is mapped from title.rendered')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "title": {
                    "rendered": "test event"
                }
            }',
            Schema::event()->name('test event')
        );
    }

    #[TestDox('event::name(null) when title is missing')]
    public function testHandlesMissingTitle()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{"id": 123}',
            Schema::event()->name(null)
        );
    }
}
