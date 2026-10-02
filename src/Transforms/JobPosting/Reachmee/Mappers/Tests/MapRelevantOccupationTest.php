<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapRelevantOccupation;

#[CoversClass(MapRelevantOccupation::class)]
final class MapRelevantOccupationTest extends TestCase
{
    #[TestDox('jobPosting::relevantOccupation is single element array occupation_area')]
    public function testMapsOccupationAreaToRelevantOccupationName(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapRelevantOccupation(),
            '{
                "occupation_area": "Software development"
            }',
            Schema::jobPosting()->relevantOccupation([
                Schema::occupation()->name('Software development')
            ])
        );
    }

    #[TestDox('jobPosting::relevantOccupation name is [] when occupation_area is missing')]
    public function testMapsMissingOccupationAreaToNullName(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapRelevantOccupation(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->relevantOccupation([])
        );
    }
}
