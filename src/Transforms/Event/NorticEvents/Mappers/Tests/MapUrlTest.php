<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapUrl;

#[CoversClass(MapUrl::class)]
final class MapUrlTest extends TestCase
{
    #[TestDox('event::url is taken from $.link')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{"link":"https://example.com/ticket/event/88731"}',
            Schema::event()->url('https://example.com/ticket/event/88731')
        );
    }
}
