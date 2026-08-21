<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\Event\AxiellEvents\AxiellEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Event.library';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('AXIELL_EVENTS_URL');
$transformer    = new AxiellEventTransform('ax-', 'https://bibliotekfh.se/evenemang#/events/', ['Digital vägledning','Läxhjälp','Rådgivning','Teknik'], []);
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new NullPaginator(), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::Event,
        'collectionClearFilter' => ['filter_by' => 'x-created-by:=municipio://schema.org-transformer/axiell-events'],
    ],
);

$storage->store($reader->read());

if (getenv('AXIELL_EVENTS_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('AXIELL_EVENTS_MONITOR_URL'));
}
