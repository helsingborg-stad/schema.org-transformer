<?php

declare(strict_types=1);

namespace SchemaTransformer\IO;

use SchemaTransformer\Interfaces\AbstractDataReader;

class HttpReader implements AbstractDataReader
{
    public function read(string $path): array
    {
        $curl = curl_init($path);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        $response = curl_exec($curl);

        if ($response === false) {
            die('Error: ' . curl_error($curl));
        }
        curl_close($curl);

        // Decode JSON response
        return json_decode($response, true);
    }
}
