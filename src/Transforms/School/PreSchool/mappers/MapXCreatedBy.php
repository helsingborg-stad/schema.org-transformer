<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

class MapXCreatedBy extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }
    public function map(Preschool $school, array $data): Preschool
    {
        return $school->setProperty('x-created-by', 'municipio://schema.org-transformer/pre-school');
    }
}
