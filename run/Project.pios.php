<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\GetParamPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\PiosProject\PiosProjectTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Project.pios';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

$httpReaderPath = getenv('PIOS_API_URL');
$apiKey         = getenv('PIOS_API_KEY');
$transformer    = new PiosProjectTransform('pios');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Accept' => 'application/json', 'ApiKey' => $apiKey ], new GetParamPaginator('pageNumber'), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::ProjectPios,
        'collectionClearFilter' => ['filter_by' => '@type:=Project'],
    ],
);

$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('PROJECT_PIOS_MONITOR_URL'));
