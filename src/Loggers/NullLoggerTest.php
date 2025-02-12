<?php

namespace SchemaTransformer\Loggers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NullLoggerTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $logger = new NullLogger();
        $this->assertInstanceOf(NullLogger::class, $logger);
    }

    #[TestDox('does not produce any output')]
    public function testDoesNotProduceAnyOutput()
    {
        $logger = new NullLogger();
        $logger->log('This is a test message');
        $this->expectOutputString('');
    }
}
