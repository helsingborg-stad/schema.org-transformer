<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapXCreatedBy;

#[CoversClass(MapXCreatedBy::class)]
final class MapXCreatedByTest extends TestCase
{
    #[TestDox('project::createdBy is hardcoded')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapXCreatedBy(),
            '{
                "id": 123
            }',
            Schema::project()->setProperty('x-created-by', 'municipio://schema.org-transformer/pios-project')
        );
    }
}
