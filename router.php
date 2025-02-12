<?php

declare(strict_types=1);

namespace SchemaTransformer;

// increase memory limit
ini_set('memory_limit', '2048M');

require_once 'vendor/autoload.php';

\SchemaTransformer\App::run(getopt("", [
    "source:",
    "sourceheaders:",
    "paginator:",
    "output:",
    "outputheaders:",
    "outputformat:",
    "transform:",
    "idprefix:",
    "authpath:",
    "authclientid:",
    "authclientsecret:",
    "authscope:"
]));
