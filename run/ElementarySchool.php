<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseClientFactory;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\School\ElementarySchool\ElementarySchoolTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'ElementarySchool';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('ELEMENTARY_SCHOOL_API_URL');
$transformer    = new ElementarySchoolTransform('R', TypesenseClientFactory::create());
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::ElementarySchool, [ 'filter_by' => '@type:=ElementarySchool' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('ELEMENTARY_SCHOOL_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('ELEMENTARY_SCHOOL_MONITOR_URL'));
}
