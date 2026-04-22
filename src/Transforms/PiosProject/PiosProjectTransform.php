<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject;

use Municipio\Schema\Schema;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapDepartment;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapDescription;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapEmployee;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapFunding;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapKeywords;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapName;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapStatus;
use SchemaTransformer\Transforms\PiosProject\Mappers\MapXCreatedBy;

class PiosProjectTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function preprocessData(array $data): array
    {
        return $data['records'] ?? [];
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapIdentifier($this),
            new MapName(),
            new MapDescription(),
            new MapFunding(),
            new MapDepartment(),
            new MapEmployee(),
            new MapStatus(),
            new MapKeywords(),
            new MapXCreatedBy()
        ];

        $result = array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($project, $mapper) use ($item) {
                    return $mapper->map($project, $item);
                },
                Schema::project()
            )->toArray();
        }, array_values($data));
        return $result;
    }
}
