<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDirectApply;

#[CoversClass(MapDirectApply::class)]
final class MapDirectApplyTest extends TestCase
{
    #[TestDox('jobPosting::directApply is true when hide_apply_button is false')]
    public function testEnablesDirectApplyWhenApplyButtonIsVisible(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "hide_apply_button": false
            }',
            Schema::jobPosting()->directApply(true)
        );
    }

    #[TestDox('jobPosting::directApply is false when hide_apply_button is true')]
    public function testDisablesDirectApplyWhenApplyButtonIsHidden(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "hide_apply_button": true
            }',
            Schema::jobPosting()->directApply(false)
        );
    }

    #[TestDox('jobPosting::directApply is false when hide_apply_button is missing')]
    public function testDisablesDirectApplyWhenHideApplyButtonIsMissing(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDirectApply(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->directApply(false)
        );
    }
}
