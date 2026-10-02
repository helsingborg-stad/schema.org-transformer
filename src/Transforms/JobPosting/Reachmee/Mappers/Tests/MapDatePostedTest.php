<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapDatePosted;

#[CoversClass(MapDatePosted::class)]
final class MapDatePostedTest extends TestCase
{
    #[TestDox('jobPosting::datePosted is taken from publishing_date')]
    public function testMapsPublishingDateToDatePosted(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDatePosted(),
            '{
                "publishing_date": "2024-03-15"
            }',
            Schema::jobPosting()->datePosted('2024-03-15')
        );
    }

    #[TestDox('jobPosting::datePosted is null when publishing_date is missing')]
    public function testMapsMissingPublishingDateToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapDatePosted(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->datePosted(null)
        );
    }
}
