<?php

namespace SchemaTransformer\Storage;

use JsonException;
use RuntimeException;

class ConsoleStorage implements StorageInterface
{
    public function store(mixed $data): void
    {
        try {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
        } catch (JsonException $exception) {
            throw new RuntimeException('Failed to encode storage data as JSON.', 0, $exception);
        }
    }
}
