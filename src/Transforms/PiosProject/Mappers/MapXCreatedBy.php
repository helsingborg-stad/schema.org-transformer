<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;

class MapXCreatedBy extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        return $project->setProperty('x-created-by', 'municipio://schema.org-transformer/pios-project');
    }
}
