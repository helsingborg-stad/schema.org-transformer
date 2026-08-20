<?php

namespace SchemaTransformer\Run\Cli;

enum Target: string
{
    case Console   = 'console';
    case Typesense = 'typesense';
}
