<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapTitle;

#[CoversClass(MapTitle::class)]
final class MapTitleTest extends TestCase
{
    #[TestDox('jobPosting::title is taken from attributes.texts.title')]
    public function testMapsNestedTitle(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTitle(),
            '{
                "attributes": {
                    "texts": {
                        "title": "Slagruteingenjör"
                    }
                }
            }',
            Schema::jobPosting()->title('Slagruteingenjör')
        );
    }

    #[TestDox('jobPosting::title is null when title is missing')]
    public function testMapsMissingTitleToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTitle(),
            '{
                "attributes": {
                    "texts": {}
                }
            }',
            Schema::jobPosting()->title(null)
        );
    }

    #[TestDox('jobPosting::title is null when attributes are missing')]
    public function testMapsMissingAttributesToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapTitle(),
            '{
                "nothing here": {}
            }',
            Schema::jobPosting()->title(null)
        );
    }
}
