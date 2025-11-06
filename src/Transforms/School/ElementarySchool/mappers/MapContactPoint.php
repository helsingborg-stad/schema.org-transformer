<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapContactPoint extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
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
