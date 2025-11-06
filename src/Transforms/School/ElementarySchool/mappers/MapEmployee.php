<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapEmployee extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        return $school->employee(
            array_map(
                fn($person) => Schema::person()
                    ->name($person['name'] ?? null)
                    ->jobTitle($person['job_title'] ?? null)
                    ->email($person['email'] ?? null)
                    ->telephone($person['telephone'] ?? null)
                    ->image(
                        isset($person['image']) && is_array($person['image']) ?
                        Schema::imageObject()
                            ->name($person['image']['name'] ?? null)
                            ->caption($person['image']['caption'] ?? null)
                            ->description($person['image']['alt'] ?? null)
                            ->url($person['image']['url'] ?? null)
                        : null
                    ),
                $data['employee'] ?? []
            )
        );
    }
}
