<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataWriter;

class HttpWriter implements AbstractDataWriter
{
    public function write(string $path, string $data, array $config = null): bool
    {
        $curl = curl_init($path);

        $headers = array_merge([
            "ACCEPT" => "Accept: application/json"
        ], $config);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data
        ]);

        $response = curl_exec($curl);

        if (false === $response) {
            return false;
        }
        curl_close($curl);

        // Decode JSON response
        return json_decode($response, true);
    }
}
