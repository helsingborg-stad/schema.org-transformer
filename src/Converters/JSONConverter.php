<?php

declare(strict_types=1);

namespace SchemaTransformer\Converters;

use SchemaTransformer\Interfaces\AbstractDataConverter;

class JSONConverter implements AbstractDataConverter
{
    public function encode(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
