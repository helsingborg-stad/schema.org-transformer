<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    /** Verify that the image URL, title and alt text come from ACF image data. */
    public function testMapsOfferingImage(): void
    {
        $data = TestHelper::fixture('offering.data.json');

        $image = (new MapImage())->map($data);

        self::assertEquals(
            Schema::imageObject()
                ->url($data['acf']['image']['url'])
                ->name($data['acf']['image']['title'])
                ->description('asdadada'),
            $image
        );
    }

    /** Verify that missing ACF data cannot produce an image. */
    public function testReturnsNullWithoutAcf(): void
    {
        $data        = TestHelper::fixture('demand.data.json');
        $data['acf'] = null;

        self::assertNull((new MapImage())->map($data));
    }
}
