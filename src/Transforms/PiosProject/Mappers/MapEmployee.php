<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;
use Municipio\Schema\Schema;

class MapEmployee extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        // NOTE: Mapping to array differs from Stratsys
        return $project->employee(
            array_values(
                array_filter(
                    array_map(
                        fn($member) => Schema::person()->description($member['role'] ?? '')->email($member['email'] ?? ''),
                        $data['teamMembers'] ?? []
                    ),
                    fn($person) => !empty($person->getProperty('email'))
                )
            )
        );
    }
}
