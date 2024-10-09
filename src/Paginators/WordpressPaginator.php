<?php

declare(strict_types=1);

namespace SchemaTransformer\Paginators;

use SchemaTransformer\Interfaces\AbstractPaginator;
use SchemaTransformer\Util\HttpUtils;

final class WordpressPaginator implements AbstractPaginator
{
    public function getNext(array $headers): string | false
    {
        if (array_key_exists("link", $headers)) {
            [$rel, $url] = HttpUtils::getLink($headers["link"]);
            // Find the link of the next page
            if ($rel == "next") {
                return $url;
            }
        }
        return false;
    }
};
