<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseClientFactory;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\School\PreSchool\PreSchoolTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'PreSchool';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('PRE_SCHOOL_API_URL');
$transformer    = new PreSchoolTransform('R', TypesenseClientFactory::create());
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::PreSchool, [ 'filter_by' => '@type:=PreSchool' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('PRE_SCHOOL_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('PRE_SCHOOL_MONITOR_URL'));
}
