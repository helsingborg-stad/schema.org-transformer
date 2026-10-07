<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmploymentType;

#[CoversClass(MapEmploymentType::class)]
final class MapEmploymentTypeTest extends TestCase
{
    #[TestDox('jobPosting::employmentType is taken from the employment-type detail')]
    public function testMapsEmploymentTypeDetail(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentType(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "texts": {
                                "details": [
                                    {
                                        "type": "employment-hours",
                                        "text": "40 timmar i veckan"
                                    },
                                    {
                                        "type": "employment-type",
                                        "text": "Heltid"
                                    }
                                ]
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->employmentType('Heltid')
        );
    }

    #[TestDox('jobPosting::employmentType is null when the employment-type detail is missing')]
    public function testMapsMissingEmploymentTypeDetailToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentType(),
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
            Schema::jobPosting()->employmentType(null)
        );
    }
}
