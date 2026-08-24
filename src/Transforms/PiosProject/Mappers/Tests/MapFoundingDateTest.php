<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapFoundingDate;

#[CoversClass(MapFoundingDate::class)]
final class MapFoundingDateTest extends TestCase
{
    #[TestDox('project::foundingDate is taken from startYear')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapFoundingDate(),
            '{
                "startYear": "2026"
            }',
            Schema::project()->foundingDate('2026')
        );
    }
}
