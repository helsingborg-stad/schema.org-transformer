<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api;

use SchemaTransformer\Interfaces\AbstractPaginator;

/**
 * Class VarbiPaginator
 * Paginator that retrieves the next page link from API responses
 * @package SchemaTransformer\Transforms\JobPosting\VarbiJobPosting\Api
 */
class VarbiPaginator implements AbstractPaginator
{
    public function getNext(string $previous, array $headers, array $response): string | false
    {
        return $response['links']['next'] ?? false;
    }
}
