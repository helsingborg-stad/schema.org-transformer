<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataReader;

/**
 * Class HttpStatusCodeReader
 * @package SchemaTransformer\IO
 *
 * This class reads data from a URL and returns it as an array.
 * It uses the HTTP status code to determine if the URL is valid.
 * If the status code is not 200, it returns false.
 */
class HttpStatusCodeReader implements AbstractDataReader
{
    public function read(string $path): array|false
    {
        if (!filter_var($path, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL: $path");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
