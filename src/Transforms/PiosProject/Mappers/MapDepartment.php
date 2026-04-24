<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapDepartment extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        $entityName = $data['entityName'] ?? null;
        return $entityName
            ? $project->department(Schema::organization()->name($entityName))
            : $project;
    }
}
