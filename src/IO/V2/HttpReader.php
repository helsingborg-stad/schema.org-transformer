<?php

declare(strict_types=1);

namespace SchemaTransformer\IO\V2;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Interfaces\AbstractPaginator;
use SchemaTransformer\IO\V2\ReaderInterface;
use SchemaTransformer\Loggers\NullLogger;
use SchemaTransformer\Paginators\NullPaginator;
use SchemaTransformer\Util\HttpUtils;

class HttpReader implements ReaderInterface
{
    public function __construct(
        private string $path,
        private AbstractDataTransform $transformer,
        private array $headers = [],
        private AbstractPaginator $paginator = new NullPaginator(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function read(): array
    {
        $result = [];
        $next   = $this->path;
        $this->logger->info("Reading data from HTTP source");

        while ($next !== false) {
            list($response, $headers) = $this->curl($next);

            if ($response === [] || $response === false || $response === null) {
                break;
            }

            $preprocessedData = $this->transformer->preprocessData($response);

            if ($preprocessedData === []) {
                break;
            }

            $transformed = $this->transformer->transform($preprocessedData);
            $result      = [...$result, ...$transformed];
            $next        = $this->paginator->getNext($next, $headers);
        };

        $this->logger->info("Finished reading data from HTTP source");
        $this->logger->info("Total records read: " . count($result));
        return $result;
    }
    protected function curl(string $path): array
    {
        $curl = curl_init($path);

        $requestHeaders = $this->formatHeaders(array_merge([
            'Accept' => 'application/json',
        ], $this->headers));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($curl, CURLOPT_HEADER, 1);

        $response = curl_exec($curl);

        if (false === $response) {
            throw new \Exception("Could not retreive source. A HTTP error occurred: " . curl_error($curl));
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

    /**
     * Format named and preformatted headers for cURL.
     *
     * @param array<int|string, mixed> $headers Request headers.
     *
     * @return array<int, string> cURL header lines.
     */
    protected function formatHeaders(array $headers): array
    {
        $formattedHeaders = [];

        foreach ($headers as $name => $value) {
            $formattedHeaders[] = is_int($name) ? (string) $value : $name . ': ' . $value;
        }

        return $formattedHeaders;
    }
}
