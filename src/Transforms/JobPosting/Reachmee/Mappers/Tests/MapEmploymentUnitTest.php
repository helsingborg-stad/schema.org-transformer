<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmploymentUnit;

#[CoversClass(MapEmploymentUnit::class)]
final class MapEmploymentUnitTest extends TestCase
{
    #[TestDox('jobPosting::employmentUnit is array mapped from organization and area data')]
    public function testMapsEmploymentUnitAndAddress(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentUnit(),
            '{
                "organizations": [
                    {
                        "nameorgunit": "Helsingborg stad"
                    },
                    {
                        "nameorgunit": "Stadsledningsförvaltningen"
                    }
                ],
                "areas": [
                    {
                        "name": "Skane"
                    },
                    {
                        "name": "Helsingborg"
                    }
                ]
            }',
            Schema::jobPosting()->employmentUnit([
                Schema::organization()
                    ->name('Stadsledningsförvaltningen')
                    ->address(
                        Schema::postalAddress()
                            ->addressRegion('Skane')
                            ->addressLocality('Helsingborg')
                    )
            ])
        );
    }

    #[TestDox('jobPosting:employmentUnit is [] when employment unit is missing')]
    public function testMapsMissingEmploymentUnitToEmptyArray(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentUnit(),
            '{
                "organizations": [
                    {
                        "nameorgunit": "Helsingborg stad"
                    }
                ]
            }',
            Schema::jobPosting()->employmentUnit([])
        );
    }
}
