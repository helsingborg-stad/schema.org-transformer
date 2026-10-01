<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api;

use Psr\Log\LoggerInterface;
use SchemaTransformer\IO\V2\HttpReader;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiDataTransform;
use SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api\VarbiPaginator;

/**
 * Class VarbiApi
 * Handles communication with the Varbi API for job postings and related data.
 * @package SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api
 */
class VarbiApi
{
    protected string $apiUrl;
    protected string $apiKey;
    protected LoggerInterface $logger;

    public function __construct(string $apiUrl, string $apiKey, LoggerInterface $logger)
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }
    public function getJobPostings(): array
    {
        return $this->fetch('');
    }
    public function getEmploymentTypes(): array
    {
        return $this->fetch('/employment_types');
    }

    protected function fetch(string $endpoint): array
    {
        return new HttpReader(
            $this->apiUrl . $endpoint,
            new VarbiDataTransform(),
            [
            'X-Api-key'       => $this->apiKey,
            'Accept-Language' => '*'
            ],
            new VarbiPaginator(),
            $this->logger
        )->read();
    }
}
