<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataReader;

class FileReader implements AbstractDataReader
{
    public function read(string $path, array $config = null): array|false
    {
        $file = file_get_contents($path);

        if (false === $file) {
            return false;
        }
        return json_decode($file, true);
    }
}
