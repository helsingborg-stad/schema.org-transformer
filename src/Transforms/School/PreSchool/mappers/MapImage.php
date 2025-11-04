<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;

class MapImage extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
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
