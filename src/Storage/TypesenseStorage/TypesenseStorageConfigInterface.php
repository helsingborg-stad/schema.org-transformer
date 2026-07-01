<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

interface TypesenseStorageConfigInterface
{
    public function getClient(): \Typesense\Client;
    public function getCollection(): TypesenseCollection;
    public function getClearStorageQueryParams(): ?array;
}
