<?php

namespace SchemaTransformer;

use SchemaTransformer\IO\V2\ReaderInterface;
use SchemaTransformer\Storage\StorageInterface;

class Config implements ConfigInterface
{
    public function __construct(
        private ReaderInterface $sourceReader,
        private StorageInterface $storage
    ) {
    }

    public function getSourceReader(): ReaderInterface
    {
        return $this->sourceReader;
    }

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }
}
