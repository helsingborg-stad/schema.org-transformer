<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use SchemaTransformer\Interfaces\AbstractDataPreprocessor;

class NullDataPreprocessor implements AbstractDataPreprocessor
{
    public function preprocessData(array $data): array
    {
        return $data;
    }
}
