<?php

namespace SchemaTransformer\Interfaces;

interface AbstractLogger
{
    public function log(string $message): void;
}
