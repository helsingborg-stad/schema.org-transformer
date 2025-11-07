<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

class MapNumberOfChildren extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return $school->numberOfChildren(
            $this->tryMapPositiveInt($data['acf']['number_of_children'] ?? null)
        );
    }
}
