<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api;

use SchemaTransformer\Interfaces\AbstractDataTransform;

/**
 * Class VarbiPaginatedDataTransform
 * Data transformer that unpacks data from API responses
 * The purpose is to extract {data: [...] } from the API response.
 * @package SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api
 */
class VarbiPaginatedDataTransform implements AbstractDataTransform
{
    public function preprocessData(array $data): array
    {
        return $data['data'] ?? [];
    }

    public function transform(array $data): array
    {
        return $data;
    }
}
