<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractDataTransform extends AbstractDataPreprocessor
{
    public function transform(array $data): array;
}
