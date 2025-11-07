<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapVideo;

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
            Schema::preschool()->video([
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
            Schema::preschool()
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
            Schema::preschool()->video([]) // Intentionally incorrect expected schema
        );
    }
}
