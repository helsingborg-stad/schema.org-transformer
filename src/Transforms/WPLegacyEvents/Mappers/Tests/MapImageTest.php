<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('event::image() is taken from featured_media')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "_embedded": {
                    "wp:featuredmedia": [
                        {
                            "source_url": "https://example.com/image.jpg",
                            "alt_text": "An example image"
                        },
                        {
                            "source_url": "https://example.com/image2.jpg",
                            "alt_text": "Another example image"
                        }
                    ]
                }
            }',
            Schema::event()->image([
                Schema::imageObject()
                    ->url('https://example.com/image.jpg')
                    ->description('An example image')
                    ->caption('An example image'),
                Schema::imageObject()
                    ->url('https://example.com/image2.jpg')
                    ->description('Another example image')
                    ->caption('Another example image'),
            ])
        );
    }

    #[TestDox('event::image(null) when featured_media is missing')]
    public function testHandlesMissingContent()
    {
        (new TestHelper())
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{"id": 123}',
                Schema::event()->image([]),
                "No image should be set if no featured media exists"
            )
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{
                    "_embedded": {
                        "wp:featuredmedia": [
                            {
                                "source_url": null,
                                "alt_text": "An example image"
                            }
                        ]
                    }
                }',
                Schema::event()->image([]),
                "No image should be set if no featured media url exists"
            )
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{
                    "_embedded": {
                        "wp:featuredmedia": [
                            {
                                "alt_text": "An example image"
                            }
                        ]
                    }
                }',
                Schema::event()->image([]),
                "No image should be set if no featured media exists"
            );
    }
}
