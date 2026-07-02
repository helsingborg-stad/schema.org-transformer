<?php

namespace SchemaTransformer\Storage;

interface StorageInterface
{
    public function store(mixed $data): void;
}
