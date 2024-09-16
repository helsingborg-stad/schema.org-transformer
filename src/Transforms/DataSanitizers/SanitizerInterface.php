<?php

namespace SchemaTransformer\Transforms\DataSanitizers;

interface SanitizerInterface
{
    public function sanitize(array $data): array;
}
