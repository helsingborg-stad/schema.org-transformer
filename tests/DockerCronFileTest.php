<?php

namespace SchemaTransformer\Tests;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class DockerCronFileTest extends TestCase
{
    private string $cronFile;

    protected function setUp(): void
    {
        $this->cronFile = __DIR__ . '/../.docker/cronjobs';
    }

    #[TestDox('cron file has empty line at the end')]
    public function testCronFileHasEmptyLineAtTheEnd()
    {
        $fileContent = file_get_contents($this->cronFile);
        static::assertStringEndsWith("\n", $fileContent);
    }
}
