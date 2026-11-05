<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\IO\V2\XmlHttpReader;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Transforms\JobPosting\Visma\VismaJobPostingTransform;
use SchemaTransformer\Webhooks\Webhooks;

$id         = 'JobPosting.visma';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

$httpReaderPath = getenv('VISMA_RECRUIT_PATH');
$guidGroup      = getenv('VISMA_RECRUIT_GUID_GROUP');
if (!is_string($httpReaderPath) || $httpReaderPath === '' || !is_string($guidGroup) || $guidGroup === '') {
    throw new \RuntimeException('VISMA_RECRUIT_PATH and VISMA_RECRUIT_GUID_GROUP must be set');
}

$collectionName = getenv('VISMA_RECRUIT_COLLECTION');
if (!is_string($collectionName) || trim($collectionName) === '') {
    $collectionName = TypesenseCollection::JobPostingPublic->value;
}

$transformer = new VismaJobPostingTransform($guidGroup);
$reader      = new XmlHttpReader($httpReaderPath, $transformer, [ 'Accept' => 'application/xml, text/xml' ], new NullPaginator(), $logger);
$storage     = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::JobPostingPublic,
        'collectionName'        => trim($collectionName),
        'collectionClearFilter' => ['filter_by' => 'x-created-by:=' . VismaJobPostingTransform::CREATED_BY],
    ],
);

$storage->store($reader->read());

(new Webhooks(logger: $logger))->trigger(getenv('VISMA_RECRUIT_MONITOR_URL'));
