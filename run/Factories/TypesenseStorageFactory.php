<?php

namespace SchemaTransformer\Run\Factories;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseStorageConfig;

class TypesenseStorageFactory
{
    public static function create(
        TypesenseCollection $collection,
        array $collectionClearFilter,
        LoggerInterface $logger
    ): TypesenseStorage {
        return new TypesenseStorage(
            new TypesenseStorageConfig(
                TypesenseClientFactory::create(),
                $collection,
                $collectionClearFilter
            ),
            $logger
        );
    }
}
