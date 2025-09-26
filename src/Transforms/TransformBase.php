<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

abstract class TransformBase
{
    protected string $idprefix;

    public function __construct(string $idprefix)
    {
        $this->idprefix = $idprefix;
    }
    public function formatId(string | int $value): string
    {
        return trim($this->idprefix . $value);
    }
    protected function isValidArray(mixed $data, string $name): bool
    {
        return  !empty($data[$name]) && is_array($data[$name]);
    }
}
