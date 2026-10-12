<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    /** Maps the image object from the ACF image field. */
    public function testMapsImageObject(): void
    {
        $result = (new MapImage())->map(Schema::SponsorOffer(), TestHelper::fixture('offering.data.json'))->toArray();

        self::assertSame('ImageObject', $result['image']['@type']);
        self::assertSame('https://example.invalid/wp-content/uploads/sites/7/2026/08/målgrupp2.png', $result['image']['url']);
        self::assertSame('asdadada', $result['image']['description']);
    }

    /** Skips image mapping when the ACF image is false. */
    public function testSkipsFalseImageField(): void
    {
        $data                 = TestHelper::fixture('offering.data.json');
        $data['acf']['image'] = false;

        $result = (new MapImage())->map(Schema::SponsorOffer(), $data)->toArray();

        self::assertArrayNotHasKey('image', $result);
    }
}
