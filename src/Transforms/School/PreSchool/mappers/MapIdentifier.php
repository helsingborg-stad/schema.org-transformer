<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\Preschool;

class MapIdentifier extends AbstractPreSchoolDataMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return $school
            ->identifier(
                $this->formatId(
                    $data['id'] ?? null ? (string)$data['id'] : null
                )
            );
    }
}
