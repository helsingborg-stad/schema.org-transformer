<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\JobPosting\Reachmee\ReachmeeJobPostingTransform;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('jobPosting::identifier is formatted from project_id')]
    public function testFormatsProjectIdAsIdentifier(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new ReachmeeJobPostingTransform('reachmee-')),
            '{
                "project_id": 123
            }',
            Schema::jobPosting()->identifier('reachmee-123')
        );
    }
}
