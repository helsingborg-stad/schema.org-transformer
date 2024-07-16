<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class FileWriter implements AbstractDataWriter
{
    public function write(string $path, string $data): array|false
    {
        $file = fopen($path, 'w');

        if (false === fwrite($file, $data)) {
            return false;
        }
        return fclose($file) === true ? [] : false;
    }
}
