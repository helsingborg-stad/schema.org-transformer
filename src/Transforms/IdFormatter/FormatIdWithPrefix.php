<?php

namespace SchemaTransformer\Transforms\IdFormatter;

use SchemaTransformer\Interfaces\AbstractIdFormatter;

class FormatIdWithPrefix implements AbstractIdFormatter
{
    public function __construct(private string $prefix)
    {
    }

    public function formatId(string $id): string
    {
        return $this->prefix . $id;
    }
}
