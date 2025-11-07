<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\ElementarySchool;

class MapIdentifier extends AbstractElementarySchoolDataMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school
            ->identifier(
                $this->formatId(
                    $data['id'] ?? null ? (string)$data['id'] : null
                )
            );
    }
}
