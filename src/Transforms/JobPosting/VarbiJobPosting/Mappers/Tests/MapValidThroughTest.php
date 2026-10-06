<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapValidThrough;

#[CoversClass(MapValidThrough::class)]
final class MapValidThroughTest extends TestCase
{
    #[TestDox('jobPosting::validThrough is taken from data.attributes.dates.deadline')]
    public function testMapsDeadlineToValidThrough(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapValidThrough(),
            '{
                "data": {
                    "attributes": {
                        "dates": {
                            "deadline": "2026-12-31"
                        }
                    }
                }
            }',
            Schema::jobPosting()->validThrough('2026-12-31')
        );
    }

    #[TestDox('jobPosting::validThrough is null when deadline is missing')]
    public function testMapsMissingDeadlineToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapValidThrough(),
            '{
                "data": {
                    "attributes": {
                        "dates": {}
                    }
                }
            }',
            Schema::jobPosting()->validThrough(null)
        );
    }

    #[TestDox('jobPosting::validThrough is null when data is missing')]
    public function testMapsMissingDataToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapValidThrough(),
            '{
                "nothing here": {}
            }',
            Schema::jobPosting()->validThrough(null)
        );
    }
}
