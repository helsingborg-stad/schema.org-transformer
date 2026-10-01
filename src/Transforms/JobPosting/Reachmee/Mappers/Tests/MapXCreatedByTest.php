<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapXCreatedBy;

#[CoversClass(MapXCreatedBy::class)]
final class MapXCreatedByTest extends TestCase
{
    #[TestDox('project::createdBy is hardcoded')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapXCreatedBy(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->setProperty('x-created-by', 'municipio://schema.org-transformer/reachmee-jobposting')
        );
    }
}
