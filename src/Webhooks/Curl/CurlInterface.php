<?php

namespace SchemaTransformer\Webhooks\Curl;

interface CurlInterface
{
    public function get(string $url): string;
    public function setHeaders(array $headers): void;
}
