<?php

declare(strict_types=1);

namespace SchemaTransformer\Util;

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Util\HttpUtils;

final class HTTPUtilsTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testGetResponseHeaders(): void
    {
        $headers =
            "HTTP/2 200\r\n" .
            "server: nginx\r\n" .
            "date: Tue, 08 Oct 2024 14:26:37 GMT\r\n" .
            "content-type: application/json; charset=UTF-8\r\n" .
            "\r\n";

        $data = HttpUtils::getResponseHeaders($headers);

        $this->assertEquals([
            "server"       => "nginx",
            "date"         => "Tue, 08 Oct 2024 14:26:37 GMT",
            "content-type" => "application/json; charset=UTF-8"
        ], $data);
    }

    public function testGetLink(): void
    {
        $data = HttpUtils::getLink("<https://wp-path?page=403>; rel=\"prev\"");

        $this->assertEquals([
            "prev",
            "https://wp-path?page=403",
        ], $data);
    }
}
