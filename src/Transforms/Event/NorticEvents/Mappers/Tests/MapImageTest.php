<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers\Tests;

use Municipio\Schema\Schema;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('event::image is taken from $.imageUrl')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{"imageUrl":"https://example.com/image.jpg"}',
            Schema::event()->image([Schema::imageObject()->url('https://example.com/image.jpg')])
        );
    }

    #[TestDox('event::image is empty when no imageUrl is provided')]
    public function testNoImage(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{}',
            Schema::event()->image([])
        );
    }
}
