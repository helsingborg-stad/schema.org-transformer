<?php

namespace SchemaTransformer\Loggers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TerminalLoggerTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $logger = new TerminalLogger();
        $this->assertInstanceOf(TerminalLogger::class, $logger);
    }

    #[TestDox('outputs message')]
    public function testOutputsMessage()
    {
        $logger = new TerminalLogger();
        $this->expectOutputRegex('/Hello, world!/');
        $logger->log('Hello, world!');
    }

    #[TestDox('appends newline to message')]
    public function testOutputsMessageWithNewline()
    {
        $logger = new TerminalLogger();
        $this->expectOutputString("Hello, world!\n");
        $logger->log('Hello, world!');
    }
}
