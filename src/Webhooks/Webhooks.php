<?php

namespace SchemaTransformer\Webhooks;

use SchemaTransformer\Webhooks\Curl\Curl;
use SchemaTransformer\Webhooks\Curl\CurlInterface;

class Webhooks implements WebhooksInterface
{
    public function __construct(private CurlInterface $curl = new Curl())
    {
    }

    public function trigger(string $url): void
    {
        $this->curl->get($url);
    }
}
