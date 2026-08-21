<?php

namespace SchemaTransformer\Run\Factories;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Run\Cli\Target;
use SchemaTransformer\Storage\ConsoleStorage;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use SchemaTransformer\Storage\TypesenseStorage\TypesenseStorage;

class StorageFactoryTest extends TestCase
{
    #[TestDox('calling create with console target returns a ConsoleStorage instance')]
    public function testCreateWithConsoleTargetReturnsConsoleStorageInstance()
    {
        $storage = StorageFactory::create(target: Target::Console);

        $this->assertInstanceOf(ConsoleStorage::class, $storage);
    }

    #[TestDox('calling create with typesense target returns a TypesenseStorage instance')]
    public function testCreateWithTypesenseTargetReturnsTypesenseStorageInstance()
    {
        $storage = StorageFactory::create(
            target: Target::Typesense,
            options: [
                'collection'            => TypesenseCollection::ElementarySchool,
                'collectionClearFilter' => ['filter_by' => '@type:=ElementarySchool'],
            ]
        );

        $this->assertInstanceOf(TypesenseStorage::class, $storage);
    }
}
