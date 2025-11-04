<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;

class MapVideo extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return $school->video(
            array_values(array_filter(array_map(
                fn($url) => Schema::videoObject()
                    ->url($url),
                array_filter([$data['acf']['video'] ?? null])
            )))
        );
    }
}
