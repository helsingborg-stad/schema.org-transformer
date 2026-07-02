<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapName;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('project::name is take from title')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "title": "Test project"
            }',
            Schema::project()->name('Test project')
        );
    }
}
