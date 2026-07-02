<?php

namespace SchemaTransformer\Webhooks\Curl;

interface CurlInterface
{
    public function get(string $url): string;
}
