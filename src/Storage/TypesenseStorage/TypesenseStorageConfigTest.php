<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Typesense\Client;

class TypesenseStorageConfigTest extends TestCase
{
    #[TestDox('returns the provided data')]
    public function testReturnsTheProvidedData(): void
    {
        $typesenseClient = $this->createMock(Client::class);
        $config          = new TypesenseStorageConfig($typesenseClient, TypesenseCollection::Event, []);

        static::assertSame($config->getClient(), $config->getClient());
        static::assertSame($config->getCollection(), $config->getCollection());
        static::assertSame($config->getClearStorageQueryParams(), $config->getClearStorageQueryParams());
    }
}
