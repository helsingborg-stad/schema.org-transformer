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
        $displayOnWebsite = $data['acf']['cta_application']['display_on_website'] ?? true;
        if (!$displayOnWebsite) {
            return $school->potentialAction([]);
        }

        $description               = $data['acf']['cta_application']['title'] ?? null;
        $disambiguatingDescription = $data['acf']['cta_application']['description'] ?? null;

        return $school->potentialAction(
            array_values(
                array_filter(
                    array_map(
                        fn ($t, $k) =>
                                is_array($t) && is_string($t['title'] ?? null) && !empty($t['title'] ?? null)
                                ? Schema::action()->name($k)->title($t['title'])->url($t['url'] ?? null)->description($description)->disambiguatingDescription($disambiguatingDescription)
                                : null,
                        ($data['acf']['cta_application'] ?? []),
                        array_keys($data['acf']['cta_application'] ?? [])
                    )
                )
            )
        );
    }
}
