<?php

namespace SchemaTransformer;

use SchemaTransformer\IO\V2\ReaderInterface;
use SchemaTransformer\Storage\StorageInterface;

interface ConfigInterface
{
    public function getSourceReader(): ReaderInterface;
    public function getStorage(): StorageInterface;
}
