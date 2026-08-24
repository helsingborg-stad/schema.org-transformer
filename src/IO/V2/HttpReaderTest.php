<?php

declare(strict_types=1);

namespace SchemaTransformer\IO\V2;

use PHPUnit\Framework\TestCase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Paginators\GetParamPaginator;

final class HttpReaderTest extends TestCase
{
    public function testFormatHeadersSupportsNamedAndPreformattedHeaders(): void
    {
        $reader        = new HttpReader(
            '',
            $this->createMock(AbstractDataTransform::class),
        );
        $formatHeaders = new \ReflectionMethod($reader, 'formatHeaders');

        $headers = $formatHeaders->invoke($reader, [
            'Accept' => 'text/plain',
            'ApiKey' => 'secret',
            'Authorization: Bearer token',
        ]);

        $this->assertSame([
            'Accept: text/plain',
            'ApiKey: secret',
            'Authorization: Bearer token',
        ], $headers);
    }

    public function testReadStopsPaginationWhenPreprocessedPageIsEmpty(): void
    {
        $transformer = $this->createMock(AbstractDataTransform::class);
        $transformer->expects($this->exactly(2))
            ->method('preprocessData')
            ->willReturnOnConsecutiveCalls([['id' => 1]], []);
        $transformer->expects($this->once())
            ->method('transform')
            ->with([['id' => 1]])
            ->willReturn([['id' => 1]]);
        $reader            = new class (
            'https://example.com/projects',
            $transformer,
            paginator: new GetParamPaginator('pageNumber'),
        ) extends HttpReader {
            /** @var array<int, array{array, array}> */
            public array $responses = [];

            /** @var array<int, string> */
            public array $requestedPaths = [];

            /**
             * Return queued HTTP responses without making network requests.
             *
             * @param string $path Requested path.
             *
             * @return array{array, array}|false Queued response and headers.
             */
            protected function curl(string $path): array|false
            {
                $this->requestedPaths[] = $path;

                return array_shift($this->responses) ?? false;
            }
        };
        $reader->responses = [
            [['records' => [['id' => 1]]], []],
            [['records' => [], 'pageNumber' => 2], []],
        ];

        $result = $reader->read();

        $this->assertSame([['id' => 1]], $result);
        $this->assertSame([
            'https://example.com/projects',
            'https://example.com/projects?pageNumber=1',
        ], $reader->requestedPaths);
    }
}
