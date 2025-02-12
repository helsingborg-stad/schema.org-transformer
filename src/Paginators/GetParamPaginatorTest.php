<?php

namespace SchemaTransformer\Paginators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Interfaces\AbstractDataReader;

class GetParamPaginatorTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(
            \SchemaTransformer\Paginators\GetParamPaginator::class,
            new \SchemaTransformer\Paginators\GetParamPaginator("page", $this->getAbstractDataReader())
        );
    }

    #[TestDox('getNext() adds page parameter to the source if it is not present')]
    public function testGetNextAddsPageParameterToSourceIfItIsNotPresent(): void
    {
        $dataReader = $this->getAbstractDataReader();
        $paginator  = new \SchemaTransformer\Paginators\GetParamPaginator("page", $dataReader);

        $dataReader->expects($this->once())->method('read')->with('http://example.com?page=1');

        $paginator->getNext("http://example.com", []);
    }

    #[TestDox('getNext() increases page number by 1 if page parameter is present')]
    public function testGetNextIncreasesPageNumberBy1IfPageParameterIsPresent(): void
    {
        $dataReader = $this->getAbstractDataReader();
        $paginator  = new \SchemaTransformer\Paginators\GetParamPaginator("page", $dataReader);

        $dataReader->expects($this->once())->method('read')->with('http://example.com?page=2');

        $paginator->getNext("http://example.com?page=1", []);
    }

    #[TestDox('getNext() returns false if the next page is not available')]
    public function testGetNextReturnsFalseIfTheNextPageIsNotAvailable(): void
    {
        $dataReader = $this->getAbstractDataReader();
        $dataReader->method('read')->willReturn(false);
        $paginator = new \SchemaTransformer\Paginators\GetParamPaginator("page", $dataReader);

        $this->assertFalse($paginator->getNext("http://example.com?page=1", []));
    }

    #[TestDox('getNext() returns the next page URL if it is available')]
    public function testGetNextReturnsTheNextPageUrlIfItIsAvailable(): void
    {
        $dataReader = $this->getAbstractDataReader();
        $dataReader->method('read')->willReturn([]);
        $paginator = new \SchemaTransformer\Paginators\GetParamPaginator("page", $dataReader);

        $this->assertEquals("http://example.com?page=2", $paginator->getNext("http://example.com?page=1", []));
    }

    private function getAbstractDataReader(): AbstractDataReader|MockObject
    {
        return $this
            ->getMockBuilder(\SchemaTransformer\Interfaces\AbstractDataReader::class)
            ->getMock();
    }
}
