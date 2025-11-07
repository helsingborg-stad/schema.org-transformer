<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapName;

#[CoversClass(MapName::class)]
final class MapNameTest extends TestCase
{
    #[TestDox('preschool::name is taken from title.rendered')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapName(),
            '{
                "title": { "rendered": "Testskolan" }
            }',
            Schema::preschool()->name('Testskolan')
        );
    }
}
