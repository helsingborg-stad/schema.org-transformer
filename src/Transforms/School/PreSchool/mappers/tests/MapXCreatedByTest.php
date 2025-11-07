<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapXCreatedBy;

#[CoversClass(MapXCreatedBy::class)]
final class MapXCreatedByTest extends TestCase
{
    #[TestDox('preSchool::x-created-by is municipio://schema.org-transformer/pre-school')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapXCreatedBy(),
            '{
                "id": "12345"
            }',
            Schema::preschool()->setProperty('x-created-by', 'municipio://schema.org-transformer/pre-school')
        );
    }
}
