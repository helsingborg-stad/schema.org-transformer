<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapNumberOfGroups;

#[CoversClass(MapNumberOfGroups::class)]
final class MapNumberOfGroupsTest extends TestCase
{
    #[TestDox('preschool::numberOfGroups is taken from acf.number_of_units')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfGroups(),
            '{
                "acf": {
                    "number_of_units": "42"
                }
            }',
            Schema::preschool()->numberOfGroups(42)
        );
    }

    #[TestDox('preschool::numberOfGroups is taken from acf.number_of_units only when positive numeric')]
    public function testNumeric(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfGroups(),
            '{
                "acf": {
                    "number_of_units": "fortytwo"
                }
            }',
            Schema::preschool()->numberOfGroups(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfGroups(),
            '{
                "acf": {
                    "number_of_units": 0
                }
            }',
            Schema::preschool()->numberOfGroups(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfGroups(),
            '{
                "acf": {
                    "number_of_units": -42
                }
            }',
            Schema::preschool()->numberOfGroups(null)
        );
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfGroups(),
            '{
                "id": 123
            }',
            Schema::preschool()->numberOfGroups(null)
        );
    }
}
