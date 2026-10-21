<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Run\Factories\TypesenseClientFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\School\ElementarySchool\ElementarySchoolTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id           = 'ElementarySchool';
$dependentIds = ['Event', 'Event.legacy'];
$logger       = new TerminalLogger($id);
$lockRunners  = array_map(fn($dependentId) => new \SchemaTransformer\LockRunner\LockRunner($dependentId, $logger), [$id,...$dependentIds]);
$options      = new \SchemaTransformer\Run\Cli\Options();

foreach ($lockRunners as $lockRunner) {
    $lockRunner->lock();
}


$httpReaderPath = getenv('ELEMENTARY_SCHOOL_API_URL');
$transformer    = new ElementarySchoolTransform('R', TypesenseClientFactory::create());
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::ElementarySchool,
        'collectionClearFilter' => ['filter_by' => '@type:=ElementarySchool'],
    ],
);

$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('ELEMENTARY_SCHOOL_MONITOR_URL'));
