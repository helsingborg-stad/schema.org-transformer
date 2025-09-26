<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\TixEvents\TixEventTransform;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('description is set from source->EventGroupId with prefix')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new TixEventTransform('tix_')),
            '{
                "EventGroupId": 123
            }',
            Schema::event()
                ->identifier('tix_123')
        );
    }
}
