<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

class MapAreaServed extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
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
