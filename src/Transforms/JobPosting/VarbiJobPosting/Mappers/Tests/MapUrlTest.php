<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapUrl;

#[CoversClass(MapUrl::class)]
final class MapUrlTest extends TestCase
{
    #[TestDox('jobPosting::url is taken from the included job-ad self link')]
    public function testMapsIncludedJobAdSelfLinkToUrl(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "links": {
                            "self": "https://example.com/jobs/123"
                        }
                    }
                ]
            }',
            Schema::jobPosting()->url('https://example.com/jobs/123')
        );
    }

    #[TestDox('jobPosting::url is null when the included job-ad self link is missing')]
    public function testMapsMissingSelfLinkToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "links": []
                    }
                ]
            }',
            Schema::jobPosting()->url(null)
        );
    }

    #[TestDox('jobPosting::url is null when the included job-ad is missing')]
    public function testMapsMissingIncludedJobAdToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapUrl(),
            '{
                "included": []
            }',
            Schema::jobPosting()->url(null)
        );
    }
}
