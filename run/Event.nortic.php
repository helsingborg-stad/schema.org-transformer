<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\Event\NorticEvents\NorticEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Event.nortic';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

$httpReaderPath = getenv('NORTIC_EVENTS_API_URL');
$transformer    = new NorticEventTransform(
    'nortic-',
    [
        // fn($item) => str_starts_with($item['organizerName'] ?? '', '????'),
    ]
);

$reader  = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new NullPaginator(), $logger);
$storage = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::Event,
        'collectionClearFilter' => ['filter_by' => 'x-created-by:=municipio://schema.org-transformer/nortic'],
    ],
);

(new \SchemaTransformer\LockRunner\LockRunner($id . '-update', $logger))->lock();
$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('NORTIC_EVENTS_MONITOR_URL'));
