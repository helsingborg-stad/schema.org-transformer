<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractDataWriter
{
    public function write(string $path, array $data): void;
}
