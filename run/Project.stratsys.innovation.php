<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\HttpWriter;
use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Services\AuthService;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\StratsysTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Project.stratsys.innovation';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('STRATSYS_INNOVATION_PATH');
$transformer    = new StratsysTransform('');
$token          = (new AuthService(new HttpWriter(["Content-Type: application/x-www-form-urlencoded"])))->getToken(getenv('STRATSYS_INNOVATION_AUTH'), getenv('STRATSYS_INNOVATION_CLIENTID'), getenv('STRATSYS_INNOVATION_CLIENTSECRET'), 'exportview.read');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', $token ], new WordpressPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::Project, [ 'filter_by' => '@type:=Project' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('STRATSYS_INNOVATION_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('STRATSYS_INNOVATION_MONITOR_URL'));
}
