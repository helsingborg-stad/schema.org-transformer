<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\PreSchoolTransform;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapIdentifier;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('preschool::identifier is taken from id and then prefixed')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new PreSchoolTransform('PRE')),
            '
            {
                "id": "12345"
            }
            ',
            Schema::preschool()->identifier('PRE12345')
        );
    }
}
