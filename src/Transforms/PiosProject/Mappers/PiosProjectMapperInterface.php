<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;

interface PiosProjectMapperInterface
{
    public function map(Project $project, array $data): Project;
}
