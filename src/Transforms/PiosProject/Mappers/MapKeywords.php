<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Project;

class MapKeywords extends AbstractPiosProjectMapper
{
    public function map(Project $project, array $data): Project
    {
        return $project->keywords(
            array_values(array_filter(
                array_map(
                    fn($tag) => $tag['displayName'] ?? null
                        ? Schema::definedTerm()
                            ->name($tag['displayName'] ?? null)
                            ->inDefinedTermSet(Schema::definedTermSet()->name('tags'))
                        : null,
                    $data['tags'] ?? []
                )
            ))
        );
    }
}
