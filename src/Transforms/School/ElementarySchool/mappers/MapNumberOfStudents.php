<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\ElementarySchool;

class MapNumberOfStudents extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school->numberOfStudents(
            $this->tryMapPositiveInt($data['acf']['number_of_students'] ?? null)
        );
    }
}
