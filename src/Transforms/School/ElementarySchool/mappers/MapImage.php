<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapImage extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school->image(
            array_map(
                fn($image) => Schema::imageObject()
                    ->name($image['title'] ?? null)
                    ->caption($image['caption'] ?? null)
                    ->description($image['alt'] ?? null)
                    ->url($image['url'] ?? null),
                $data['images'] ?? []
            )
        );
    }
}
