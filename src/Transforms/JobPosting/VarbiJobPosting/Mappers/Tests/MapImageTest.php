<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('jobPosting::image is taken from included.attributes.media.top_image')]
    public function testMapsToImageFromIncludedMediaTopImage(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "included": [
                    {
                        "type": "job-ad",
                        "attributes": {
                            "media": {
                                "top_image": "https://example.com/image.jpg",
                                "top_image_alt_text": "Alt text"
                            }
                        }
                    }
                ]
            }',
            Schema::jobPosting()->image([
                Schema::imageObject()
                    ->url("https://example.com/image.jpg")
                    ->name("Alt text")
                    ->caption("Alt text")
            ])
        );
    }
    #[TestDox('jobPosting::image is empty')]
    public function testMapsToEmptyImageList(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->image([])
        );
    }
}
