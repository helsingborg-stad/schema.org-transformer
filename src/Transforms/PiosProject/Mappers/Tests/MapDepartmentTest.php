<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapDepartment;

#[CoversClass(MapDepartment::class)]
final class MapDepartmentTest extends TestCase
{
    #[TestDox('project::department is take from entityName')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDepartment(),
            '{
                "entityName": "Test department"
            }',
            Schema::project()->department(
                Schema::organization()->name('Test department')
            )
        );
    }
}
