<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\WordpressPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\WPExhibitionEventTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'ExhibitionEvent';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

$httpReaderPath = getenv('WORDPRESS_EXHIBITION_EVENT_PATH');
$transformer    = new WPExhibitionEventTransform();
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new WordpressPaginator(), $logger);
$storage        = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::ExhibitionEvent,
        'collectionClearFilter' => ['filter_by' => '@type:=ExhibitionEvent'],
    ],
);

$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('WORDPRESS_EXHIBITION_EVENTS_MONITOR_URL'));
