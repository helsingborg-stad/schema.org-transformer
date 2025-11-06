<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapNumberOfStudents;

#[CoversClass(MapNumberOfStudents::class)]
final class MapNumberOfStudentsTest extends TestCase
{
    #[TestDox('elementarySchool::numberOfStudents is taken from acf:number_of_students')]
    public function testItWorks(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfStudents(),
            '{
                "acf": {
                    "number_of_students": 250
                }
            }',
            Schema::elementarySchool()->numberOfStudents(250)
        );
    }

    #[TestDox('elementarySchool::numberOfStudents ignores negative or zero values')]
    public function testIsIgnoresNegativeOrZeroValues(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfStudents(),
            '{
                "acf": {
                    "number_of_students": -10
                }
            }',
            Schema::elementarySchool()->numberOfStudents(null)
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfStudents(),
            '{
                "acf": {
                    "number_of_students": 0
                }
            }',
            Schema::elementarySchool()->numberOfStudents(null)
        );
    }

    #[TestDox('elementarySchool::numberOfStudents ignores missing or non-numeric values')]
    public function testIsIgnoresMissingOrNonNumericValues(): void
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfStudents(),
            '{
                "acf": {
                    "number_of_students": "not a number"
                }
            }',
            Schema::elementarySchool()->numberOfStudents(null)
        );

        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapNumberOfStudents(),
            '{
                "acf": {
                }
            }',
            Schema::elementarySchool()->numberOfStudents(null)
        );
    }
}
