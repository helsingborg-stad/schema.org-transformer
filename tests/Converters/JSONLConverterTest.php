<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Converters\JSONLConverter;

final class JSONLConverterTest extends TestCase
{
    public function testConvertArrayToJSONL(): void
    {
        $converter = new JSONLConverter();

        $data = [
            ["a" => "a", "b" => "b", "c" => ["a", "b", "c"]],
            ["a" => "a", "b" => "b"],
        ];

        $this->assertEquals($converter->encode($data), "{\"a\":\"a\",\"b\":\"b\",\"c\":[\"a\",\"b\",\"c\"]}\n{\"a\":\"a\",\"b\":\"b\"}");
    }
}
