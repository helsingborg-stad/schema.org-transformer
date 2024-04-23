<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class FileWriter implements AbstractDataWriter
{
    public function write(string $path, array $data): void
    {
        $file = fopen($path, 'w');
        fwrite($file, json_encode($data, JSON_PRETTY_PRINT));
        fclose($file);
    }
}
