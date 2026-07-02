<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;
use SchemaTransformer\Transforms\TransformBase;

class MapIdentifier extends AbstractPiosProjectMapper
{
    public function __construct(private ?TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Project $project, array $data): Project
    {
        // Implement the mapping logic here
        return $project->identifier($this->formatId($data['projectId'] ?? ''));
    }
}
