<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapDatePosted;

#[CoversClass(MapDatePosted::class)]
final class MapDatePostedTest extends TestCase
{
    #[TestDox('jobPosting::datePosted is taken from data.attributes.dates.published')]
    public function testMapsPublishedDateToDatePosted(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDatePosted(),
            '{
                "data": {
                    "attributes": {
                        "dates": {
                            "published": "2026-10-06"
                        }
                    }
                }
            }',
            Schema::jobPosting()->datePosted('2026-10-06')
        );
    }

    #[TestDox('jobPosting::datePosted is null when published date is missing')]
    public function testMapsMissingPublishedDateToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDatePosted(),
            '{
                "data": {
                    "attributes": {
                        "dates": {}
                    }
                }
            }',
            Schema::jobPosting()->datePosted(null)
        );
    }

    #[TestDox('jobPosting::datePosted is null when data is missing')]
    public function testMapsMissingDataToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDatePosted(),
            '{
                "nothing here": {}
            }',
            Schema::jobPosting()->datePosted(null)
        );
    }
}
