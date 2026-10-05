<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapWorkHours;

#[CoversClass(MapWorkHours::class)]
final class MapWorkHoursTest extends TestCase
{
    #[TestDox('jobPosting::workHours is taken from working_hours')]
    public function testMapsWorkingHoursToWorkHours(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapWorkHours(),
            '{
                "working_hours": "40 hours per week"
            }',
            Schema::jobPosting()->workHours('40 hours per week')
        );
    }

    #[TestDox('jobPosting::workHours is null when working_hours is missing')]
    public function testMapsMissingWorkingHoursToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapWorkHours(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->workHours(null)
        );
    }
}
