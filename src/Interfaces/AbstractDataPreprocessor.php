<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractDataPreprocessor
{
    public function preprocessData(array $data): array;
}
