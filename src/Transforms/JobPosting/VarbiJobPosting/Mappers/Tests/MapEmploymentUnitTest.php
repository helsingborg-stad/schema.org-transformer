<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmploymentUnit;

#[CoversClass(MapEmploymentUnit::class)]
final class MapEmploymentUnitTest extends TestCase
{
    #[TestDox('jobPosting::employmentUnit is taken from organization details')]
    public function testMapsOrganizationDetailsToEmploymentUnits(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentUnit(),
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
                                        "type": "organization",
                                        "text": "Huvudkontoret"
                                    },
                                    {
                                        "type": "organization",
                                        "text": "Annexet"
                                    }
                                ]
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->employmentUnit([
                Schema::organization()->name('Huvudkontoret'),
                Schema::organization()->name('Annexet')
            ])
        );
    }

    #[TestDox('jobPosting::employmentUnit is empty when there are no organization details')]
    public function testMapsMissingOrganizationDetailsToEmptyEmploymentUnits(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentUnit(),
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
            Schema::jobPosting()->employmentUnit([])
        );
    }
}
