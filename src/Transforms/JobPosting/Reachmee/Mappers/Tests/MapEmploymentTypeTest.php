<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmploymentType;

#[CoversClass(MapEmploymentType::class)]
final class MapEmploymentTypeTest extends TestCase
{
    #[TestDox('jobPosting::employmentType is taken from occupation_degree')]
    public function testMapsOccupationDegreeToEmploymentType(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentType(),
            '{
                "occupation_degree": "Heltid, tillsvidare"
            }',
            Schema::jobPosting()->employmentType('Heltid, tillsvidare')
        );
    }

    #[TestDox('jobPosting::employmentType is null when occupation_degree is missing')]
    public function testMapsMissingOccupationDegreeToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmploymentType(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->employmentType(null)
        );
    }
}
