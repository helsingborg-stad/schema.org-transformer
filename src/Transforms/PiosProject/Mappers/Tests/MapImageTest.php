<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('project::image is taken from customDimensions with image URLs')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "customDimensions": [
                    {
                        "name": "En bild",
                        "value": "https://example.com/bild.jpg"
                    },
                    {
                        "name": "Ett annat dokument bild",
                        "value": "https://example.com/spreadsheet.xlsx"
                    },
                    {
                        "name": "En bild till",
                        "value": "https://example.com/image2.jpg"
                    }
                ]
            }',
            Schema::project()->image([
                Schema::imageObject()->url("https://example.com/bild.jpg")->caption("En bild")->description("En bild"),
                Schema::imageObject()->url("https://example.com/image2.jpg")->caption("En bild till")->description("En bild till")
            ])
        );
    }
}
