<?php

namespace SchemaTransformer\Loggers;

use SchemaTransformer\Interfaces\AbstractLogger;

class NullLogger implements AbstractLogger
{
    public function log(string $message): void
    {
    }
}
