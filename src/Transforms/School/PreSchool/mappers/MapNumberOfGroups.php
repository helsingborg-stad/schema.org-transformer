<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

class MapNumberOfGroups extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return $school->numberOfGroups(
            (is_numeric($data['acf']['number_of_units'] ?? null) && (int)($data['acf']['number_of_units']) > 0)
                ? (int)($data['acf']['number_of_units'])
                : null
        );
    }
}
