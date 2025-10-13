<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('event::image is taken from source basic properties')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "EventGroupId": 123,
                "SubTitle": "Event title that goes into images",
                "HasFeaturedImage": true,
                "FeaturedImagePath": "https://example.com/image1.jpg"
            }',
            Schema::event()->image([
                Schema::imageObject()
                    ->url('https://example.com/image1.jpg')
                    ->name('Event title that goes into images')
                    ->description('Event title that goes into images')
                    ->caption('Event title that goes into images')
            ])
        );
    }

    #[TestDox('event::image([]) if no maping from source can be done')]
    public function testNoImages()
    {
        (new TestHelper())
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{
                    "EventGroupId": 123
                }',
                Schema::event()->image([]),
                'No image should be set if no source image data exists'
            )
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{
                    "EventGroupId": 123,
                    "SubTitle": "Event title that goes into images",
                    "HasFeaturedImage": false,
                    "FeaturedImagePath": "https://example.com/image1.jpg"
                }',
                Schema::event()->image([]),
                'No image should be set if HasFeaturedImage is false'
            )
            ->expectMapperToConvertSourceTo(
                new MapImage(),
                '{
                    "EventGroupId": 123,
                    "SubTitle": "Event title that goes into images",
                    "HasFeaturedImage": true
                }',
                Schema::event()->image([]),
                'No image should be set if no FeaturedImagePath exists'
            );
    }
}
