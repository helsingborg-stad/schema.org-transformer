<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\HttpWriter;
use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Services\AuthService;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\StratsysTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Project.stratsys.innovation';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

$httpReaderPath = getenv('STRATSYS_INNOVATION_PATH');
$transformer    = new StratsysTransform('');
$token          = (new AuthService(new HttpWriter(["Content-Type: application/x-www-form-urlencoded"])))->getToken(getenv('STRATSYS_INNOVATION_AUTH'), getenv('STRATSYS_INNOVATION_CLIENTID'), getenv('STRATSYS_INNOVATION_CLIENTSECRET'), 'exportview.read');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', $token ], new WordpressPaginator(), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::Project,
        'collectionClearFilter' => ['filter_by' => '@type:=Project'],
    ],
);

$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('STRATSYS_INNOVATION_MONITOR_URL'));
