<?php

namespace SchemaTransformer\LockRunner;

class LockRunnerException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
