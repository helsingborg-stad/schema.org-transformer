<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapVideo extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
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
