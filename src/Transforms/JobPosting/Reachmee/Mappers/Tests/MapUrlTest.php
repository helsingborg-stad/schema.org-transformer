<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapUrl;

#[CoversClass(MapUrl::class)]
final class MapUrlTest extends TestCase
{
    #[TestDox('jobPosting::url is taken from link')]
    public function testMapsLinkToUrl(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "link": "https://example.com/jobs/123"
            }',
            Schema::jobPosting()->url('https://example.com/jobs/123')
        );
    }

    #[TestDox('jobPosting::url is empty when link is missing')]
    public function testMapsMissingLinkToEmptyUrl(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->url('')
        );
    }
}
