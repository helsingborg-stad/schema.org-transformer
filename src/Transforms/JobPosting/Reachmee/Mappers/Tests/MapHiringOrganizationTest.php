<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapHiringOrganization;

#[CoversClass(MapHiringOrganization::class)]
final class MapHiringOrganizationTest extends TestCase
{
    #[TestDox('jobPosting::hiringOrganization is mapped from the first organization')]
    public function testMapsFirstOrganizationAsHiringOrganization(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapHiringOrganization(),
            '{
                "organizations": [
                    {
                        "nameorgunit": "Testförvaltning"
                    }
                ],
                "suffix_text": "our ethics policy"
            }',
            Schema::jobPosting()->hiringOrganization([
                Schema::organization()
                    ->name('Testförvaltning')
                    ->ethicsPolicy('our ethics policy')
            ])
        );
    }

    #[TestDox('jobPosting:hiringOrganization is [] when the first organization is missing')]
    public function testMapsMissingFirstOrganizationToEmptyArray(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapHiringOrganization(),
            '{
                "organizations": []
            }',
            Schema::jobPosting()->hiringOrganization([])
        );
    }
}
