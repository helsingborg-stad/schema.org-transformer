<?php

declare(strict_types=1);

namespace SchemaTransformer\Util;

class HttpUtils
{
    public static function getResponseHeaders(string $data): array
    {
        $headers = array();
        $list = explode("\r\n", trim($data));
        array_shift($list);

        foreach ($list as $value) {
            if (false !== ($matches = explode(':', $value, 2))) {
                $headers["{$matches[0]}"] = trim($matches[1]);
            }
        }
        return $headers;
    }

    public static function getLink(string $link): array
    {
        // Split link into rows:
        // <https://wp-path?page=403>; rel="prev", 
        // <https://wp-path?page=405>; rel="next"
        $components = explode(',', trim($link));
        foreach ($components as $row) {
            // Split row by semicolon 
            [$url, $rel] = explode(';', trim($row), 2);
            // Trim characters
            $url = trim($url, " <>");
            $rel = trim(explode("=", $rel)[1], "\" ");
            return [$rel, $url];
        }
    }
}
