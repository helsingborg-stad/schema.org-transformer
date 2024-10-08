<?php

declare(strict_types=1);

namespace SchemaTransformer\Paginators;

use SchemaTransformer\Interfaces\AbstractPaginator;

final class WordpressPaginator implements AbstractPaginator
{
    public function getNext(array $headers): string | false
    {
        if (array_key_exists("link", $headers)) {
            // Split link into rows:
            // <https://wp-path?page=403>; rel="prev", 
            // <https://wp-path?page=405>; rel="next"
            $components = explode(',', $headers["link"]);
            foreach ($components as $row) {
                // Split row by semicolon 
                list($url, $rel) = explode(';', $row, 2);
                // Trim characters
                $url = trim($url, " <>");
                $rel = trim(explode("=", $rel)[1], "\" ");
                // Find the link of the next page
                if ($rel == "next") {
                    return $url;
                }
            }
        }
        return false;
    }
};
