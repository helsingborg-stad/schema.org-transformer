<?php

namespace SchemaTransformer\Transforms\IdFormatter;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FormatIdWithPrefixTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $formatter = new FormatIdWithPrefix('prefix-');
        $this->assertInstanceOf(FormatIdWithPrefix::class, $formatter);
    }

    #[TestDox('formatId method returns formatted ID')]
    public function testFormatIdReturnsFormattedId()
    {
        $formatter   = new FormatIdWithPrefix('prefix-');
        $formattedId = $formatter->formatId('123');
        $this->assertEquals('prefix-123', $formattedId);
    }
}
