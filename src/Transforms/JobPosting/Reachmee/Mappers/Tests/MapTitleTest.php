<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapTitle;

#[CoversClass(MapTitle::class)]
final class MapTitleTest extends TestCase
{
    #[TestDox('jobPosting::title is taken from title')]
    public function testMapsTitle(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTitle(),
            '{
                "title": "Senior Software Engineer"
            }',
            Schema::jobPosting()->title('Senior Software Engineer')
        );
    }

    #[TestDox('jobPosting::title is null when title is missing')]
    public function testMapsMissingTitleToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTitle(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->title(null)
        );
    }
}
