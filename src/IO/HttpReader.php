<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataReader;

class HttpReader implements AbstractDataReader
{
    private array $headers;
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function read(string $path): array|false
    {
        $curl = curl_init($path);

        $headers = array_merge([
            "ACCEPT" => "Accept: application/json"
        ], $this->headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        if (false === $response) {
            return false;
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode >= 400) {
            throw new \Exception("Could not retrive source. A HTTP error occurred: " . $httpCode);
        }

        curl_close($curl);

        // Decode JSON response
        return json_decode($response, true);
    }
}
