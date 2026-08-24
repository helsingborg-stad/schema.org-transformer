<?php

namespace SchemaTransformer\Webhooks;

use Override;
use PHPUnit\Framework\Attributes\TestDox;
use SchemaTransformer\Webhooks\Curl\CurlInterface;

class WebhooksTest extends \PHPUnit\Framework\TestCase
{
    private static function getCurl(): CurlInterface
    {
        return new class implements CurlInterface {
            public array $curledUrls = [];
            public function get(string $url): string
            {
                $this->curledUrls[] = $url;
                return $url;
            }
            #[Override]
            public function setHeaders(array $headers): void
            {
                throw new \Exception('Not implemented');
            }
        };
    }

    public function testTrigger(): void
    {
        $curl = static::getCurl();
        $url  = 'https://example.com/webhook';

        // Call the trigger method
        $webhooks = new Webhooks($curl);

        $webhooks->trigger($url);

        static::assertSame([$url], $curl->curledUrls);
    }

    #[TestDox('does not call webhook when URL is null')]
    public function testTriggerWithNullUrl(): void
    {
        $curl = static::getCurl();

        // Call the trigger method with null URL
        $webhooks = new Webhooks($curl);
        $webhooks->trigger(null);

        static::assertSame([], $curl->curledUrls);
    }

    #[TestDox('does not call webhook when URL is not a valid URL')]
    public function testTriggerWithEmptyUrl(): void
    {
        $curl = static::getCurl();

        // Call the trigger method with empty URL
        $webhooks = new Webhooks($curl);
        $webhooks->trigger('');
        $webhooks->trigger('not-a-valid-url');
        $webhooks->trigger('http://');
        $webhooks->trigger(123);

        static::assertSame([], $curl->curledUrls);
    }
}
