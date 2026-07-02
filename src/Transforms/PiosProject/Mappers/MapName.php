<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;

class MapName extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        return $project->name($data['title'] ?? '');
    }
}
