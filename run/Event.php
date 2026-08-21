<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Event';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('WP_EVENTS_API_URL');
$transformer    = new WPHeadlessEventTransform('WPH-');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::Event,
        'collectionClearFilter' => ['filter_by' => 'x-created-by:=municipio://schema.org-transformer/wp-headless'],
    ],
);

$storage->store($reader->read());

if (getenv('WP_EVENTS_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('WP_EVENTS_MONITOR_URL'));
}
