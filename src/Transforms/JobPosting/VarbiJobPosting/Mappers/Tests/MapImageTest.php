<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Mappers\MapImage;

#[CoversClass(MapImage::class)]
final class MapImageTest extends TestCase
{
    #[TestDox('jobPosting::image is empty')]
    public function testMapsToEmptyImageList(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapImage(),
            '{
                "id": 123
            }',
            Schema::jobPosting()->image([])
        );
    }
}
