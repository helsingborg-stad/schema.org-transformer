<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapNumberOfChildren;

#[CoversClass(MapNumberOfChildren::class)]
final class MapNumberOfChildrenTest extends TestCase
{
    #[TestDox('preschool::numberOfChildren is taken from acf.number_of_children')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfChildren(),
            '{
                "acf": {
                    "number_of_children": "42"
                }
            }',
            Schema::preschool()->numberOfChildren(42)
        );
    }

    #[TestDox('preschool::numberOfChildren is taken from acf.number_of_children only when positive numeric')]
    public function testNumeric(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfChildren(),
            '{
                "acf": {
                    "number_of_children": "fortytwo"
                }
            }',
            Schema::preschool()->numberOfChildren(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfChildren(),
            '{
                "acf": {
                    "number_of_children": 0
                }
            }',
            Schema::preschool()->numberOfChildren(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfChildren(),
            '{
                "acf": {
                    "number_of_children": -42
                }
            }',
            Schema::preschool()->numberOfChildren(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfChildren(),
            '{
                "id": 123
            }',
            Schema::preschool()->numberOfChildren(null)
        );
    }
}
