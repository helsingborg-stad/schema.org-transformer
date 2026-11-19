<?php

namespace SchemaTransformer\Paginators;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class WordpressPaginatorTest extends TestCase
{
    #[TestWith(['Link'])]
    #[TestWith(['link'])]
    public function testFindsNextLinkRegardlessOfHeaderCase(string $header): void
    {
        $headers = [$header => '<https://example.com?page=1>; rel="prev", <https://example.com?page=3>; rel="next"'];

        $this->assertSame('https://example.com?page=3', (new WordpressPaginator())->getNext('', $headers, []));
    }

    public function testStopsWhenThereIsNoNextLink(): void
    {
        $headers = ['Link' => '<https://example.com?page=1>; rel="prev"'];

        $this->assertFalse((new WordpressPaginator())->getNext('', $headers, []));
    }
}
