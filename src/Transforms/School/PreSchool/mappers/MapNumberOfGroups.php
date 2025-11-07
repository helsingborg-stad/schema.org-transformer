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
            $this->tryMapPositiveInt($data['acf']['number_of_units'] ?? null)
        );
    }
}
