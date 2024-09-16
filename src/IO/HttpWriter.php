<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class HttpWriter implements AbstractDataWriter
{
    private array $headers;
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }
    public function write(string $path, string $data): array|false
    {
        $curl = curl_init($path);

        $headers = array_merge([
            // "Content-Type: application/json"
        ], $this->headers);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "POST",
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $data
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl) !== 0 || curl_getinfo($curl, CURLINFO_HTTP_CODE) !== 200) {
            return false;
        }
        curl_close($curl);

        return json_decode($response, true);
    }
}
