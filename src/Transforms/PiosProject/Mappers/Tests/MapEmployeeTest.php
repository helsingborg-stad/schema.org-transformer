<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapEmployee;

#[CoversClass(MapEmployee::class)]
final class MapEmployeeTest extends TestCase
{
    #[TestDox('project::employee is taken from projectManagers')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{
                "projectManagers": ["p@example.com"]

            }',
            Schema::project()->employee([
                Schema::person()->email('p@example.com')
            ])
        );
    }

    #[TestDox('project::employee is empty when projectManagers is empty')]
    public function testEmpty()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{
                "projectManagers": []
            }',
            Schema::project()->employee([])
        );
    }

    #[TestDox('project::employee is empty when projectManagers is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{"id":123}',
            Schema::project()->employee([])
        );
    }
}
