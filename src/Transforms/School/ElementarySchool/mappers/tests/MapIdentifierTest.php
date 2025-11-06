<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\ElementarySchoolTransform;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapIdentifier;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('elementarySchool::identifier is taken from id and then prefixed')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new ElementarySchoolTransform('ELE')),
            '
            {
                "id": "12345"
            }
            ',
            Schema::elementarySchool()->identifier('ELE12345')
        );
    }
}
