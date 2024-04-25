<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class ConsoleWriter implements AbstractDataWriter
{
    public function write(string $path, array $data): bool
    {
        print(json_encode($data, JSON_PRETTY_PRINT));
        return true;
    }
}
