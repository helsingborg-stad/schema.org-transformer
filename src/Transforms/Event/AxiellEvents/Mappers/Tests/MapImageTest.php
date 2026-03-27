<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('event::image is taken from $.images')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "images": [{
                    "imageUrl": "https://example.com/image.jpg",
                    "imageCaption": "Example image caption"
                },
                {
                    "imageUrl": "https://example.com/image2.jpg"
                },
                {
                    "imageCaption": "no url so skip this"
                }]
            }',
            Schema::event()->image([
                Schema::imageObject()
                    ->url('https://example.com/image.jpg')
                    ->description('Example image caption')
                    ->caption('Example image caption'),
                Schema::imageObject()
                    ->url('https://example.com/image2.jpg')
                    ->description(null)
                    ->caption(null)
            ])
        );
    }

    #[TestDox('event::image([]) when $.images is missing')]
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
