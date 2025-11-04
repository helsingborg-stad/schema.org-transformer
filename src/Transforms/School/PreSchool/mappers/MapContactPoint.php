<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\PreSchoolDataMapperInterface;

class MapContactPoint extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
    {
        return $school->contactPoint(
            array_values(
                array_filter(
                    [
                        $data['acf']["link_facebook"] ?? null ? Schema::contactPoint()->name('facebook')->contactType('socialmedia')->url($data['acf']["link_facebook"]) : null,
                        $data['acf']["link_instagram"] ?? null ? Schema::contactPoint()->name('instagram')->contactType('socialmedia')->url($data['acf']["link_instagram"]) : null,
                    ]
                )
            )
        );
    }
}
