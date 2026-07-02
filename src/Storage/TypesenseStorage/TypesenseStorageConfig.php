<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

use Override;
use Typesense\Client;

class TypesenseStorageConfig implements TypesenseStorageConfigInterface
{
    public function __construct(
        private Client $typesenseClient,
        private TypesenseCollection $collection,
        private ?array $clearStorageQueryParams = null
    ) {
    }

    public function getClient(): Client
    {
        return $this->typesenseClient;
    }

    public function getCollection(): TypesenseCollection
    {
        return $this->collection;
    }

    public function getClearStorageQueryParams(): ?array
    {
        return $this->clearStorageQueryParams;
    }
}
