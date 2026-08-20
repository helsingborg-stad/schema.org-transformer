<?php

namespace SchemaTransformer\Storage;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ConsoleStorageTest extends TestCase
{
    #[TestDox('outputs data as JSON')]
    public function testOutputsDataAsJson(): void
    {
        $storage = new ConsoleStorage();
        $data    = [
            [
                '@type' => 'Event',
                'name'  => 'Opening night',
            ],
        ];

        $this->expectOutputString(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL);

        $storage->store($data);
    }
}
