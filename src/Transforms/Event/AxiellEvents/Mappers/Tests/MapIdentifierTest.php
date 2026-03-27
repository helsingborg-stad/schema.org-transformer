<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\AxiellEventTransform;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('event::identifier is taken from $.id')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new AxiellEventTransform('ax-', 'https://example.com/events/')),
            '{
                "id": "12345"
            }',
            Schema::event()->identifier('ax-12345')
        );
    }
}
