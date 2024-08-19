<?php

declare(strict_types=1);

namespace SchemaTransformer\Interfaces;

interface AbstractAuth
{
    public function getToken(string $path, string $clientId, string $clientSecret, string $clientScope): string|false;
}
