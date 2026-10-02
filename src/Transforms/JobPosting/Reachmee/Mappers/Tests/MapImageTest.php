<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\Reachmee\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('jobPosting::image is taken from image_link')]
    public function testMapsImageLinkToImage(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "image_link": "https://example.com/jobs/123.jpg"
            }',
            Schema::jobPosting()->image('https://example.com/jobs/123.jpg')
        );
    }

    #[TestDox('jobPosting::image is null when image_link is missing')]
    public function testMapsMissingImageLinkToNull(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->image(null)
        );
    }
}
