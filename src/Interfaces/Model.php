<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractModel
{
    public function transformData(array $data): array;
}
