<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\VarbiJobPostingTransform;

#[CoversClass(MapIdentifier::class)]
final class MapIdentifierTest extends TestCase
{
    #[TestDox('jobPosting::identifier is formatted from data.id')]
    public function testFormatsDataIdAsIdentifier(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapIdentifier(new VarbiJobPostingTransform('varbi-')),
            '{
                "data": {
                    "id": 12345
                }
            }',
            Schema::jobPosting()->identifier('varbi-12345')
        );
    }
}
