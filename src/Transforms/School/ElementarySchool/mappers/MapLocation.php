<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapLocation extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school->location(array_filter(
            array_values(
                array_map(
                    fn ($address) =>
                        $address['address'] ?? null
                        ? Schema::place()
                            ->name($address['address']['name'] ?? null)
                            ->address($address['address']['address'] ?? null)
                            ->latitude($address['address']['lat'] ?? null)
                            ->longitude($address['address']['lng'] ?? null)
                            ->description($address['address']['description'] ?? null)
                        : null,
                    ($data['acf']['visiting_address'] ?? [])
                )
            )
        ));
    }
}
