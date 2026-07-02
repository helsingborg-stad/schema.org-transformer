<?php

namespace SchemaTransformer\Webhooks;

use SchemaTransformer\Webhooks\Curl\CurlInterface;

class WebhooksTest extends \PHPUnit\Framework\TestCase
{
    public function testTrigger(): void
    {
        $curl = new class implements CurlInterface {
            public array $curledUrls = [];
            public function get(string $url): string
            {
                $this->curledUrls[] = $url;
                return $url;
            }
        };

        $url = 'https://example.com/webhook';

        // Call the trigger method
        $webhooks = new Webhooks($curl);

        $webhooks->trigger($url);

        static::assertSame([$url], $curl->curledUrls);
    }
}
