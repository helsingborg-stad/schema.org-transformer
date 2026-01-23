<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('event::image is constructed from _embedded.acf:attachment.*.full')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "_embedded": {
                    "acf:attachment": [
                        {
                            "media_type": "image",
                            "media_details": {
                                "sizes": {
                                    "full": {
                                        "source_url": "https://example.com/image.jpg"
                                    }
                                }
                            },
                            "title": {
                                "rendered": "Test event"
                            },
                            "alt_text": "Test event alt text"
                        }
                    ]
                }
            }',
            Schema::event()->image([
                Schema::imageObject()
                    ->url("https://example.com/image.jpg")
                    ->name("Test event")
                    ->description("Test event alt text")
                    ->caption("Test event")
            ])
        );
    }

    #[TestDox('event::image([]) when missing _embedded.acf:attachment.*.full')]
    public function testNoFullImages()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "_embedded": {
                    "acf:attachment": [
                        {
                            "media_type": "image",
                            "media_details": {
                                "sizes": {
                                    "thumbnail": {
                                        "source_url": "https://example.com/image-thumb.jpg"
                                    }
                                }
                            },
                            "title": {
                                "rendered": "Test event"
                            },
                            "alt_text": "Test event alt text"
                        }
                    ]
                }
            }',
            Schema::event()->image([])
        );
    }

    #[TestDox('event::image([]) when missing _embedded')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "id": 123
            }',
            Schema::event()->image([])
        );
    }
}
