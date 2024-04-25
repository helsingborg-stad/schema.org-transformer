<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class FileWriter implements AbstractDataWriter
{
    public function write(string $path, array $data): bool
    {
        $file = fopen($path, 'w');

        if (false === fwrite($file, json_encode($data, JSON_PRETTY_PRINT))) {
            return false;
        }
        return fclose($file);
    }
}
