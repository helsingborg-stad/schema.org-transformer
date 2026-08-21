<?php

namespace SchemaTransformer\Webhooks;

use Psr\Log\LoggerInterface;
use SchemaTransformer\Webhooks\Curl\Curl;
use SchemaTransformer\Webhooks\Curl\CurlInterface;

class Webhooks implements WebhooksInterface
{
    public function __construct(
        private CurlInterface $curl = new Curl(),
        private LoggerInterface $logger = new \SchemaTransformer\Loggers\NullLogger()
    ) {
    }

    public function trigger(?string $url = null): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        $this->curl->get($url);
        $this->logger->info("Webhook triggered for URL: " . $url);
    }
}
