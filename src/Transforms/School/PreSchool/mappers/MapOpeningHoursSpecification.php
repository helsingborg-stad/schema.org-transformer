<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;

class MapOpeningHoursSpecification extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return ($data['acf']['open_hours']['open'] ?? null)
        && ($data['acf']['open_hours']['close'] ?? null)
        ?
            $school->openingHoursSpecification(
                Schema::openingHoursSpecification()
                    ->opens($data['acf']['open_hours']['open'] ?? null)
                    ->closes($data['acf']['open_hours']['close'] ?? null)
            )
            : $school;
    }
}
