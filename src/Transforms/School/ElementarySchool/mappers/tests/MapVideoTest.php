<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapVideo;

#[CoversClass(MapVideo::class)]
final class MapVideoTest extends TestCase
{
    #[TestDox('elementarySchool::video is taken from acf.video when set')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapVideo(),
            '{
                "acf": {
                    "video": "https://youtu.be/dQw4w9WgXcQ"
                }
            }',
            Schema::elementarySchool()->video([
                Schema::videoObject()
                    ->url('https://youtu.be/dQw4w9WgXcQ')
            ])
        );
    }
}
