<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TypesenseDataChunker::class)]
class TypesenseDataChunkerTest extends TestCase
{
    #[TestDox('chunks the data correctly')]
    public function testReturnsTheProvidedData(): void
    {
        $chunker = new TypesenseDataChunker();
        $data    = range(1, 250);
        $chunks  = $chunker->getChunks($data, 100);
        $this->assertCount(3, $chunks);
        $this->assertCount(100, $chunks[0]);
        $this->assertCount(100, $chunks[1]);
        $this->assertCount(50, $chunks[2]);
    }

    #[TestDox('returns the data as a single chunk if it is not iterable')]
    public function testNonIterableData(): void
    {
        $chunker = new TypesenseDataChunker();
        $data    = 123;
        $chunks  = $chunker->getChunks($data, 100);
        $this->assertCount(1, $chunks);
        $this->assertEquals($data, $chunks[0]);
    }
}
