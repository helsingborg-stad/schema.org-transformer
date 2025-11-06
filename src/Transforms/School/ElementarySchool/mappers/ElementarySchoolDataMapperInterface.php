<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\ElementarySchool;

interface ElementarySchoolDataMapperInterface
{
    public function map(ElementarySchool $school, array $data): ElementarySchool;
}
