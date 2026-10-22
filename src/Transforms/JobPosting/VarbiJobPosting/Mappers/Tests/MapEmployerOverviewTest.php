<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapEmployerOverview;

#[CoversClass(MapEmployerOverview::class)]
final class MapEmployerOverviewTest extends TestCase
{
    #[TestDox('jobPosting::employerOverview is empty')]
    public function testLeavesJobPostingUnchanged(): void
    {
        // TODO: Make this test do something
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployerOverview(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->employerOverview(null)
        );
    }
}
