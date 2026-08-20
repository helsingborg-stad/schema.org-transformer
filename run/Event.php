<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Event';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options($argv);

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('WP_EVENTS_API_URL');
$transformer    = new WPHeadlessEventTransform('WPH-');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::Event, [ 'filter_by' => 'x-created-by:municipio://schema.org-transformer/wp-headless' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('WP_EVENTS_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('WP_EVENTS_MONITOR_URL'));
}
