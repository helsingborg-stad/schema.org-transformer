<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\WPExhibitionEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'ExhibitionEvent';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('WORDPRESS_EXHIBITION_EVENT_PATH');
$transformer    = new WPExhibitionEventTransform();
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::ExhibitionEvent, [ 'filter_by' => '@type:=ExhibitionEvent' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL'));
}
