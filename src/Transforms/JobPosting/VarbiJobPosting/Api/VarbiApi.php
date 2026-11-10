<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api;

use Psr\Log\LoggerInterface;
use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiDataTransform;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiPaginatedDataTransform;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiPaginator;
use SchemaTransformer\Paginators\NullPaginator;

/**
 * Class VarbiApi
 * Handles communication with the Varbi API for job postings and related data.
 * @package SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api
 */
class VarbiApi
{
    protected string $visibility;
    protected string $apiUrl;
    protected string $apiKey;
    protected LoggerInterface $logger;

    public function __construct(string $visibility, string $apiUrl, string $apiKey, LoggerInterface $logger)
    {
        $this->visibility = $visibility ?? 'PUBLIC' ?? 'INTERNAL';
        $this->apiUrl     = $apiUrl;
        $this->apiKey     = $apiKey;
        $this->logger     = $logger;
    }

    public function getAllJobsWithAds(): array
    {
        $jobPostings = $this->getJobPostings();

        return array_values(array_filter(array_map(
            fn($jobPosting) => $this->fetch('/' . $jobPosting['id'] . '?include=ad,taxonomy'),
            $jobPostings ?? []
        )));
    }

    public function getJobPostings(): array
    {
        return $this->fetchPaginated('?visibility=' . $this->visibility);
    }

    protected function fetch(string $endpoint): array
    {
        return (new HttpReader(
            $this->apiUrl . $endpoint,
            new VarbiDataTransform(),
            [
                'X-Api-key'       => $this->apiKey,
                'Accept-Language' => '*',
                'Accept'          => 'application/json'
            ],
            new NullPaginator(),
            $this->logger
        ))->read();
    }
    protected function fetchPaginated(string $endpoint): array
    {
        return (new HttpReader(
            $this->apiUrl . $endpoint,
            new VarbiPaginatedDataTransform(),
            [
                'X-Api-key'       => $this->apiKey,
                'Accept-Language' => '*',
                'Accept'          => 'application/json'
            ],
            new VarbiPaginator(),
            $this->logger
        ))->read();
    }
}
