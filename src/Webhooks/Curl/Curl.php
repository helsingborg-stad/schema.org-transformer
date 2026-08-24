<?php

namespace SchemaTransformer\Webhooks\Curl;

use Override;

class Curl implements CurlInterface
{
    private \CurlHandle $ch;

    public function __construct()
    {
        $this->ch = curl_init();
    }

    public function setHeaders(array $headers): void
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function get(string $url): string
    {
        curl_setopt_array($this->ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($this->ch);

        return $response;
    }
}
