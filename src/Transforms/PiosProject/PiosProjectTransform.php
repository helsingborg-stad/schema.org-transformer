<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\PiosProject;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\TransformBase;

class PiosProjectTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function transform(array $data): array
    {
        return $data;
    }
}
