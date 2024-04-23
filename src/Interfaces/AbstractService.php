<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractService
{
    public function execute(string $input, string $output): void;
}
