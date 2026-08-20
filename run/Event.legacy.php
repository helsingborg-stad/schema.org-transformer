<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\WPLegacyEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'Event.legacy';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$cliOptions = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('WP_LEGACY_EVENTS_API_URL')  . '&start_date=' . date('Y-m-d', strtotime('-1 month'));
$transformer    = new WPLegacyEventTransform('L');
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);

$storage = $cliOptions->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::Event, [ 'filter_by' => 'x-created-by:=municipio://schema.org-transformer/wp-legacy' ], $logger)
    : new \SchemaTransformer\Storage\ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('WP_LEGACY_EVENTS_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('WP_LEGACY_EVENTS_MONITOR_URL'));
}
