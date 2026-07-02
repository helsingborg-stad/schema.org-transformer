<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;

class MapFoundingDate extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        return $project->foundingDate($data['startYear'] ?? '');
    }
}
