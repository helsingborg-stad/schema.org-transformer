<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

interface PreSchoolDataMapperInterface
{
    public function map(Preschool $school, array $data): Preschool;
}
