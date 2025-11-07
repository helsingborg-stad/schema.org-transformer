<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapAfterSchoolCare extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return ($data['acf']['open_hours_leisure_center']['open'] ?? null) && ($data['acf']['open_hours_leisure_center']['close'] ?? null)
            ? $school
                ->afterSchoolCare(
                    Schema::service()
                        ->name('Fritidsverksamhet')
                        ->description('Öppettider för fritidsverksamhet')
                        ->hoursAvailable(
                            Schema::openingHoursSpecification()
                                ->opens($data['acf']['open_hours_leisure_center']['open'] ?? null)
                                ->closes($data['acf']['open_hours_leisure_center']['close'] ?? null)
                        )
                )
            : $school;
    }
}
