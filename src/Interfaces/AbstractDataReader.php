<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractDataReader
{
    public function read(string $path): array;
}
