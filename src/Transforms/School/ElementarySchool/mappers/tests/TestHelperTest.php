<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapVideo;

#[CoversClass(TestHelper::class)]
final class TestHelperTest extends TestCase
{
    #[TestDox('expectMapperToConvertSourceTo works as expected')]
    public function testExpectMapperToConvertSourceTo(): void
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

    #[TestDox('expectMapperToConvertSourceTo fails on invalid JSON')]
    public function testExpectMapperToConvertSourceToInvalidJson(): void
    {
        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapVideo(),
            '{ invalid json ',
            Schema::elementarySchool()
        );
    }

    #[TestDox('expectMapperToConvertSourceTo fails on mismatched schema')]
    public function testExpectMapperToConvertSourceToMismatchedSchema(): void
    {
        $this->expectException(\PHPUnit\Framework\ExpectationFailedException::class);
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapVideo(),
            '{
                "acf": {
                    "video": "https://youtu.be/dQw4w9WgXcQ"
                }
            }',
            Schema::elementarySchool()->video([]) // Intentionally incorrect expected schema
        );
    }
}
