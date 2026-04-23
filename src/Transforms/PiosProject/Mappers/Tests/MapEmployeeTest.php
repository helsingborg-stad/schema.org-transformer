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
    #[TestDox('project::name is take from teamMembers')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{
                "teamMembers": [
                    {
                        "email": "p@example.com",
                        "role": "Project manager"
                    }
                ]
            }',
            Schema::project()->employee([
                Schema::person()->email('p@example.com')->description('Project manager')
            ])
        );
    }

    #[TestDox('project::employee is empty when teamMembers is empty')]
    public function testEmpty()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{
                "teamMembers": []
            }',
            Schema::project()->employee([])
        );
    }

    #[TestDox('project::employee is empty when teamMembers is missing')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployee(),
            '{"id":123}',
            Schema::project()->employee([])
        );
    }
}
