<?php

declare(strict_types=1);

namespace SchemaTransformer\Converters;

use SchemaTransformer\Interfaces\AbstractDataConverter;

class JSONLConverter implements AbstractDataConverter
{
    public function encode(array $data): string
    {
        $jsonl = [];
        foreach ($data as &$row) {
            $jsonl[] = json_encode($row);
        }
        return implode("\n", $jsonl);
    }
}
