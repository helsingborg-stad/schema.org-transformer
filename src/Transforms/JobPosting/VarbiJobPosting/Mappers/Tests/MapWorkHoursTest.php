<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapWorkHours;

#[CoversClass(MapWorkHours::class)]
final class MapWorkHoursTest extends TestCase
{
    #[TestDox('jobPosting::workHours is taken from the working-hours detail')]
    public function testMapsWorkingHoursDetail(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapWorkHours(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "texts": {
                                "details": [
                                    {
                                        "type": "employment-type",
                                        "text": "Heltid"
                                    },
                                    {
                                        "type": "working-hours",
                                        "text": "40 timmar i veckan"
                                    }
                                ]
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->workHours('40 timmar i veckan')
        );
    }

    #[TestDox('jobPosting::workHours is null when the working-hours detail is missing')]
    public function testMapsMissingWorkingHoursDetailToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapWorkHours(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "texts": {
                                "details": []
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->workHours(null)
        );
    }

    #[TestDox('jobPosting::workHours is null when the included job-ad is missing')]
    public function testMapsMissingIncludedJobAdToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapWorkHours(),
            '{
                "included": []
            }',
            Schema::jobPosting()->workHours(null)
        );
    }
}
