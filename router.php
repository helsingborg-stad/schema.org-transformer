<?php

declare(strict_types=1);

namespace SchemaTransformer;

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
    "authscope:",
    "typesense_apikey:",
    "typesense_host:",
    "typesense_port:"
]));
