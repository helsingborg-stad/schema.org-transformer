<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapDepartment extends AbstractPiosProjectMapper
{
    // Drivs av
    public function map(Project $project, array $data): Project
    {
        $entityName = $this->tryGetCustomDimensionValue($data, 'Drivs av') ?? $this->tryGetEntityName($data) ?? null;
        return $entityName
            ? $project->department(Schema::organization()->name($entityName))
            : $project;
    }

    private function tryGetEntityName(array $data): ?string
    {
        return $data['entityName'] ?? null;
    }
    private function tryGetCustomDimensionValue(array $data, string $name): ?string
    {
        $values = array_values(
            array_filter(
                array_map(
                    fn ($cd) => ($cd['name'] ?? null) === $name ? $cd['value'] ?? null : null,
                    $data['customDimensions'] ?? []
                )
            )
        );
        return $values[0] ?? null;
    }
}
