<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('elementarySchool::image is taken from images array in source')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '
            {
                "images": [
                    {
                        "ID": 1,
                        "title": "Bildtitel 1",
                        "caption": "Bildtext 1",
                        "alt": "Alternativ text 1",
                        "url": "https://skolan.se/image1.jpg"
                    },
                    {
                        "ID": 2,
                        "title": "Bildtitel 2",
                        "caption": "Bildtext 2",
                        "alt": "Alternativ text 2",
                        "url": "https://skolan.se/image2.jpg"
                    }
                ]
            }
            ',
            Schema::elementarySchool()
                ->image([
                    Schema::imageObject()
                        ->name('Bildtitel 1')
                        ->caption('Bildtext 1')
                        ->description('Alternativ text 1')
                        ->url('https://skolan.se/image1.jpg'),
                    Schema::imageObject()
                        ->name('Bildtitel 2')
                        ->caption('Bildtext 2')
                        ->description('Alternativ text 2')
                        ->url('https://skolan.se/image2.jpg')
                ])
        );
    }
}
