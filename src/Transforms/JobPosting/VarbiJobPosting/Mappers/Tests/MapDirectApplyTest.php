<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDirectApply;

#[CoversClass(MapDirectApply::class)]
final class MapDirectApplyTest extends TestCase
{
    #[TestDox('jobPosting::directApply is true when an application link is present')]
    public function testEnablesDirectApplyWhenApplicationLinkIsPresent(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "data": {
                    "links": {
                        "apply": "https://example.com/apply/123"
                    }
                }
            }',
            Schema::jobPosting()->directApply(true)
        );
    }

    #[TestDox('jobPosting::directApply is false when the application link is empty')]
    public function testDisablesDirectApplyWhenApplicationLinkIsEmpty(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "data": {
                    "links": {
                        "apply": ""
                    }
                }
            }',
            Schema::jobPosting()->directApply(false)
        );
    }

    #[TestDox('jobPosting::directApply is false when the application link is missing')]
    public function testDisablesDirectApplyWhenApplicationLinkIsMissing(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "nothing here": {
                }
            }',
            Schema::jobPosting()->directApply(false)
        );
    }
}
