<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapUrl;

#[CoversClass(MapUrl::class)]
final class MapUrlTest extends TestCase
{
    #[TestDox('event::url is mapped from event_link')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "event_link": "http://example.com/event"
            }',
            Schema::event()->url('http://example.com/event')
        );
    }

    #[TestDox('event::url(null) when event_link is missing')]
    public function testHandlesMissingEventLink()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{"id": 123}',
            Schema::event()->url(null)
        );
    }
}
