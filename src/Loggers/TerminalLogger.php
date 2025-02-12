<?php

namespace SchemaTransformer\Loggers;

use SchemaTransformer\Interfaces\AbstractLogger;

class TerminalLogger implements AbstractLogger
{
    public function log(string $message): void
    {
        echo $message . PHP_EOL;
    }
}
