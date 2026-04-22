<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapFunding extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        // NOTE: Mapping to array differs from Stratsys
        return $project->funding(
            array_values(
                array_filter(
                    array_map(
                        fn($period) => $period['amount'] ?? null
                        ? Schema::monetaryGrant()->name($period['year'] ?? '')->amount($period['amount'] ?? 0)
                        : null,
                        $data['periods'] ?? []
                    )
                )
            )
        );
    }
}
