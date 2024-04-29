<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractDataConverter
{
    public function encode(array $data): string;
}
