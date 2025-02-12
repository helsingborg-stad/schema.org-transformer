<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataReader;
use SchemaTransformer\Interfaces\AbstractLogger;
use SchemaTransformer\Interfaces\AbstractPaginator;
use SchemaTransformer\Util\HttpUtils;

class HttpReader implements AbstractDataReader
{
    private array $headers;
    private AbstractPaginator $paginator;

    public function __construct(
        AbstractPaginator $paginator,
        private AbstractLogger $logger,
        array $headers = [],
    ) {
        $this->headers   = $headers;
        $this->paginator = $paginator;
    }
    public function read(string $path): array|false
    {
        $result = [];

        $next = $path;
        while ($next !== false) {
            $this->logger->log("👀 Reading from source: " . $next);

            list($response, $headers) = $this->curl($next);
            // Extend list
            $result = [...$result, ...$response];
            // Get next page
            $next = $this->paginator->getNext($next, $headers);
        };

        $this->logger->log("✅ Read " . count($result) . " items from source");

        return $result;
    }
    protected function curl(string $path): array|false
    {
        $curl = curl_init($path);

        $reqHeaders = array_merge([
            "ACCEPT" => "Accept: application/json"
        ], $this->headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $reqHeaders);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        if (false === $response) {
            return false;
        }

        $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($code >= 400) {
            throw new \Exception("Could not retreive source. A HTTP error occurred: " . $code);
        }
        $resHeaders = HttpUtils::getResponseHeaders(substr($response, 0, $size));
        $body       = substr($response, $size);

        curl_close($curl);

        // Decode JSON response
        return [json_decode($body, true), $resHeaders];
    }
}
