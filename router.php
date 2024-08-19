<?php

declare(strict_types=1);

namespace SchemaTransformer;

require_once 'vendor/autoload.php';

\SchemaTransformer\App::run(getopt("", [
    "source:",
    "sourceheaders:",
    "output:",
    "outputheaders:",
    "outputformat:",
    "transform:",
    "authpath:",
    "authclientid:",
    "authclientsecret:",
    "authscope:"
]));
