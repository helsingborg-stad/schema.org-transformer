<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Run\Factories\TypesenseStorageFactory;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\ReachmeeJobPostingTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'JobPosting.public';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

if (!$lockRunner->lock()) {
    return;
}

$httpReaderPath = getenv('REACHMEE_HELSINGBORG_PATH');
$transformer    = new ReachmeeJobPostingTransform();
$reader         = new HttpReader($httpReaderPath, $transformer, [ 'Content-Type' => 'application/json', 'Accept' => 'application/json', ], new NullPaginator(), $logger);

$storage = $options->getTarget() === \SchemaTransformer\Run\Cli\Target::Typesense
    ? TypesenseStorageFactory::create(TypesenseCollection::JobPostingPublic, [ 'filter_by' => '@type:=JobPosting' ], $logger)
    : new ConsoleStorage($logger);

$storage->store($reader->read());

if (getenv('REACHMEE_HELSINGBORG_MONITOR_URL')) {
    (new Webhooks())->trigger(getenv('REACHMEE_HELSINGBORG_MONITOR_URL'));
}
