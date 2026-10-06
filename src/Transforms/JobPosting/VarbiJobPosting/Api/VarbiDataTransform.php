<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api;

use SchemaTransformer\Interfaces\AbstractDataTransform;

/**
 * Class VarbiDataTransform
 * Data transformer that unpacks data from API responses
 * @package SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api
 */
class VarbiDataTransform implements AbstractDataTransform
{
    public function preprocessData(array $data): array
    {
        return $data;
    }

    public function transform(array $data): array
    {
        return $data;
    }
}
