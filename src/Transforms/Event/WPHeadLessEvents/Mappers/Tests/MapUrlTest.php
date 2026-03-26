<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapUrl;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapUrl::class)]
final class MapUrlTest extends TestCase
{
    #[TestDox('event::url is constructed from acf.url')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "acf": {
                    "url": "http://example.com/event"
                }
            }',
            Schema::event()->url('http://example.com/event')
        );
    }

    #[TestDox('event::url(null) when acf.url is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "id": 123
            }',
            Schema::event()->url(null)
        );
    }
    #[TestDox('event::url(null) when acf.url is empty')]
    public function testEmpty()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "acf": {
                    "url": ""
                }
            }',
            Schema::event()->url(null)
        );
    }
}
