<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapAfterSchoolCare;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapAreaServed;

#[CoversClass(MapAfterSchoolCare::class)]
final class MapAreaServedTest extends TestCase
{
    #[TestDox('elementarySchool::areaServed is taken from _embedded->acf:term with taxonomy area')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapAreaServed(),
            '
            {
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Område A",
                            "taxonomy": "area"
                        },
                        {
                            "name": "x",
                            "taxonomy": "y"
                        },
                        {
                            "name": "Område B",
                            "taxonomy": "area"
                        }
                    ]
                }
            }
        ',
            Schema::elementarySchool()
                ->areaServed(['Område A', 'Område B'])
        );
    }
}
