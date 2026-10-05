<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapValidThrough;

#[CoversClass(MapValidThrough::class)]
final class MapValidThroughTest extends TestCase
{
    #[TestDox('jobPosting::validThrough is taken from expiration_date')]
    public function testMapsExpirationDateToValidThrough(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapValidThrough(),
            '{
                "expiration_date": "2025-12-31"
            }',
            Schema::jobPosting()->validThrough('2025-12-31')
        );
    }

    #[TestDox('jobPosting::validThrough is null when expiration_date is missing')]
    public function testMapsMissingExpirationDateToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapValidThrough(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->validThrough(null)
        );
    }
}
