<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapEmployerOverview;

#[CoversClass(MapEmployerOverview::class)]
final class MapEmployerOverviewTest extends TestCase
{
    #[TestDox('jobPosting::employerOverview is single string array taken from prefix_text')]
    public function testMapsPrefixTextToEmployerOverview(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployerOverview(),
            '{
                "prefix_text": "About the employer"
            }',
            Schema::jobPosting()->employerOverview([ 'About the employer' ])
        );
    }

    #[TestDox('jobPosting::employerOverview is [] when prefix_text is missing')]
    public function testMapsMissingPrefixTextToEmptyArray(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapEmployerOverview(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->employerOverview([])
        );
    }
}
