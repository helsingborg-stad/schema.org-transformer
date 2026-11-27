<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\NorticEvents\NorticEventTransform;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('event::identifier is taken from $.id and prefixed')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new NorticEventTransform('nortic_')),
            '{"id": 88731}',
            Schema::event()->identifier('nortic_88731')
        );
    }
}
