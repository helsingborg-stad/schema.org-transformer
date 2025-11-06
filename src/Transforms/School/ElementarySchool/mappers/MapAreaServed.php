<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\ElementarySchool;

class MapAreaServed extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school->areaServed(array_values(array_filter(
            array_map(
                fn ($t) =>
                        !empty($t) && is_string($t['name'] ?? null) && !empty($t['name'] ?? null) && ($t['taxonomy'] ?? null) === 'area'
                        ? $t['name']
                        : null,
                ($data['_embedded']['acf:term'] ?? [])
            )
        )));
    }
}
