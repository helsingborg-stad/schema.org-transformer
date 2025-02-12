<?php

declare(strict_types=1);

namespace SchemaTransformer\Paginators;

use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractPaginator;

final class GetParamPaginator implements AbstractPaginator
{
    public function __construct(private string $pageParameter, private AbstractDataReader $reader)
    {
    }

    public function getNext(string $previous, array $headers): string | false
    {
        $nextPageNumber = $this->getPreviousPageNumber($previous) + 1;

        $nextUrl = $this->applyPageParameter($previous, $nextPageNumber);

        if ($this->reader->read($nextUrl) !== false) {
            return $nextUrl;
        }

        return false;
    }

    private function getPreviousPageNumber(string $previous): int
    {
        $url = parse_url($previous);
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            if (isset($query[$this->pageParameter])) {
                return (int) $query[$this->pageParameter];
            }
        }

        return 0;
    }

    private function applyPageParameter(string $url, int $pageNumber): string
    {
        $url = parse_url($url);

        if (isset($url['query'])) {
            parse_str($url['query'], $query);
        } else {
            $query = [];
        }

        $query[$this->pageParameter] = $pageNumber;

        $url['query'] = http_build_query($query);
        $path         = $url['path'] ?? '';

        return $url['scheme'] . '://' . $url['host'] . $path . '?' . $url['query'];
    }
}
