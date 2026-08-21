<?php

namespace SchemaTransformer\Run\Factories;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Loggers\NullLogger;
use SchemaTransformer\Run\Cli\Target;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\StorageInterface;

class StorageFactory
{
    public static function create(
        Target $target,
        array $options = [],
        LoggerInterface $logger = new NullLogger()
    ): StorageInterface {
        return match ($target) {
            Target::Console => new ConsoleStorage(),
            Target::Typesense => TypesenseStorageFactory::create(
                collection: $options['collection'] ?? null,
                collectionClearFilter: $options['collectionClearFilter'] ?? null,
                logger: $logger
            )
        };
    }
}
