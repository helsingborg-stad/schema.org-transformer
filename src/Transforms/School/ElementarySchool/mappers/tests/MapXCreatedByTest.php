<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapName;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapXCreatedBy;

#[CoversClass(MapXCreatedBy::class)]
final class MapXCreatedByTest extends TestCase
{
    #[TestDox('elementarySchool::x-created-by is municipio://schema.org-transformer/elementary-school')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapXCreatedBy(),
            '{
                "id": "12345"
            }',
            Schema::elementarySchool()->setProperty('x-created-by', 'municipio://schema.org-transformer/elementary-school')
        );
    }
}
