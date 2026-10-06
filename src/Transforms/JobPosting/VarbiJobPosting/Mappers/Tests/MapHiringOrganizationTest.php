<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapHiringOrganization;

#[CoversClass(MapHiringOrganization::class)]
final class MapHiringOrganizationTest extends TestCase
{
    #[TestDox('jobPosting::hiringOrganization is taken from organization details')]
    public function testMapsOrganizationDetailsToHiringOrganizations(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapHiringOrganization(),
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
            Schema::jobPosting()->hiringOrganization([
                Schema::organization()->name('Huvudkontoret'),
                Schema::organization()->name('Annexet')
            ])
        );
    }

    #[TestDox('jobPosting::hiringOrganization is empty when there are no organization details')]
    public function testMapsMissingOrganizationDetailsToEmptyHiringOrganizations(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapHiringOrganization(),
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
            Schema::jobPosting()->hiringOrganization([])
        );
    }
}
