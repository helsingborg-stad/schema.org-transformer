<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\PiosProjectTransform;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapIdentifier;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('project::identifier is taken from projectId')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new PiosProjectTransform("pios-project-")),
            '{
                "projectId": "12345"
            }',
            Schema::project()->identifier('pios-project-12345')
        );
    }
}
