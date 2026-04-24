<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject\Mappers;

use Municipio\Schema\Project;
use SchemaTransformer\Transforms\TransformBase;

abstract class AbstractPiosProjectMapper implements PiosProjectMapperInterface
{
    public function __construct(private ?TransformBase $transform = null)
    {
    }

    abstract public function map(Project $project, array $data): Project;

    protected function formatId(string | int $value): string
    {
        return $this->transform->formatId($value);
    }
}
