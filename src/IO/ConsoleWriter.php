<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class ConsoleWriter implements AbstractDataWriter
{
    public function write(string $path, string $data): array|false
    {
        print($data);
        return [];
    }
}
