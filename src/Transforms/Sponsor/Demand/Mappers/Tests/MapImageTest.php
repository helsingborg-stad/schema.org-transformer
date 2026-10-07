<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    /** Maps image URL, title, and alternative text. */
    public function testMapsImageObject(): void
    {
        $result = (new MapImage())->map(Schema::SponsorDemandEvent(), TestHelper::fixture())->toArray();

        self::assertSame(
            [
                '@type'       => 'ImageObject',
                'url'         => 'https://example.invalid/wp-content/uploads/sites/7/2026/08/målgrupp2.png',
                'name'        => 'målgrupp2',
                'description' => 'asdadada',
            ],
            $result['image'],
        );
    }

    /** Omits an image when the ACF subfield is false. */
    public function testSkipsFalseImageField(): void
    {
        $data                 = TestHelper::fixture();
        $data['acf']['image'] = false;

        $result = (new MapImage())->map(Schema::SponsorDemandEvent(), $data)->toArray();

        self::assertArrayNotHasKey('image', $result);
    }
}
