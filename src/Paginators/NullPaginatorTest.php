<?php

declare(strict_types=1);

namespace SchemaTransformer\Paginators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class NullPaginatorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $nullPaginator = new NullPaginator();
        $this->assertInstanceOf(NullPaginator::class, $nullPaginator);
    }

    #[TestDox('getNext method returns false')]
    public function testGetNextMethodReturnsFalse()
    {
        $nullPaginator = new NullPaginator();
        $this->assertFalse($nullPaginator->getNext('previous', ['headers']));
    }
}
