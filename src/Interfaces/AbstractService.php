<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractService
{
    public function execute(string $source, string $destination): bool;
}
