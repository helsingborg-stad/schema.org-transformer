<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SchemaTransformer\Run\Factories\StorageFactory;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Loggers\TerminalLogger;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiApi;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\VarbiJobPostingTransform;

$id         = 'JobPosting.varbi.public';
$logger     = new TerminalLogger($id);
$lockRunner = new \SchemaTransformer\LockRunner\LockRunner($id, $logger);
$options    = new \SchemaTransformer\Run\Cli\Options();

$lockRunner->lock();

/**
 * We are joining multiple results from Varbi so we can aggregate them into a single dataset.
 * This allows us to work with a unified view of the data across different endpoints.
 */
$varbiApi    = new VarbiApi(getenv('VARBI_JOB_POSTING_API_URL'), getenv('VARBI_JOB_POSTING_API_KEY'), $logger);
$jobs        = $varbiApi->getAllJobsWithAds();
$transformer = new VarbiJobPostingTransform('varbi-');

$storage = StorageFactory::create(
    target: $options->getTarget(),
    logger: $logger,
    options: [
        'collection'            => TypesenseCollection::JobPostingPublic,
        'collectionClearFilter' => ['filter_by' => 'x-created-by:=municipio://schema.org-transformer/varbi-jobposting'],
    ],
);

$storage->store($transformer->transform($jobs));

// show raw paginated job postings from the API
// $storage->store($varbiApi->getJobPostings());

// show aggregation of all job postings with ads
// $storage->store($jobs);
