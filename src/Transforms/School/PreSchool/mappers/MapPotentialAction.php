<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;

class MapPotentialAction extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        $description = $data['acf']['cta_application']['description'] ?? null;

        return $school->potentialAction(
            array_values(
                array_filter(
                    array_map(
                        fn ($t, $k) =>
                                is_array($t) && is_string($t['title'] ?? null) && !empty($t['title'] ?? null)
                                ? Schema::action()->name($k)->title($t['title'])->description($description)->url($t['url'] ?? null)
                                : null,
                        ($data['acf']['cta_application'] ?? []),
                        array_keys($data['acf']['cta_application'] ?? [])
                    )
                )
            )
        );
    }
}
