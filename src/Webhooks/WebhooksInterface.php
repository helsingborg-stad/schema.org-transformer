<?php

namespace SchemaTransformer\Webhooks;

interface WebhooksInterface
{
    public function trigger(string $url): void;
}
