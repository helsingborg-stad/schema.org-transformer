<?php

namespace SchemaTransformer\Webhooks\Curl;

class Curl implements CurlInterface
{
    public function get(string $url): string
    {
        curl_setopt_array($ch = curl_init(), [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        unset($ch);

        return $response;
    }
}
