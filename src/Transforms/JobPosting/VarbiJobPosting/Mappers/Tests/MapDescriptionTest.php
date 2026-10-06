<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDescription;

#[CoversClass(MapDescription::class)]
final class MapDescriptionTest extends TestCase
{
    #[TestDox('jobPosting::description is taken from the included job-ad combined description')]
    public function testMapsCombinedDescriptionToTextObject(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "texts": {
                                "descriptions": {
                                    "combined": "A detailed role description."
                                }
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->description([
                Schema::textObject()->text('A detailed role description.')
            ])
        );
    }

    #[TestDox('jobPosting::description is empty when the included job-ad is missing')]
    public function testMapsMissingIncludedJobAdToEmptyDescription(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "included": []
            }',
            Schema::jobPosting()->description([])
        );
    }

    #[TestDox('jobPosting::description is empty when combined description is empty')]
    public function testFiltersEmptyCombinedDescription(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDescription(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "texts": {
                                "descriptions": {
                                    "combined": ""
                                }
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->description([])
        );
    }
}
