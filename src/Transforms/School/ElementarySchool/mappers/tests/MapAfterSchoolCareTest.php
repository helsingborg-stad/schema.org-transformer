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
final class MapAfterSchoolCareTest extends TestCase
{
    #[TestDox('elementarySchool::afterSchoolCare is taken from _embedded->acf:term with taxonomy area')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapAfterSchoolCare(),
            '{
                "acf": {
                    "open_hours_leisure_center": {
                        "open": "06:00:00",
                        "close": "18:00:00"
                    }
                }
            }',
            Schema::elementarySchool()
                ->afterSchoolCare(Schema::service()
                    ->name('Fritidsverksamhet')
                    ->description('Öppettider för fritidsverksamhet')
                    ->hoursAvailable(
                        Schema::openingHoursSpecification()
                            ->opens("06:00:00")
                            ->closes("18:00:00")
                    ))
        );
    }
}
