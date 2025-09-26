<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\WPLegacyEvents\WPLegacyEventTransform2;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('event::identifier is constructed from from id with prefix')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new WPLegacyEventTransform2('TheBestAndBiggestPrefix')),
            '{
                "id": 123
            }',
            Schema::event()->identifier('TheBestAndBiggestPrefix123')
        );
    }
}
