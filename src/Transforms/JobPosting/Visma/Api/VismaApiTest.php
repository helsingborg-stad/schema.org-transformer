<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Visma\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(VismaApi::class)]
final class VismaApiTest extends TestCase
{
    private const LIST_URL = 'https://example.test/list';

    #[TestDox('returns each list assignment together with its detail item')]
    public function testReturnsAssignmentsWithItems(): void
    {
        $api = $this->api([
            self::LIST_URL     => '<Assignments><Assignment><Guid>A</Guid><RefNo>1</RefNo></Assignment></Assignments>',
            'guidAssignment=A' => '<Assignments><Assignment><Guid>A</Guid><Detail>x</Detail></Assignment></Assignments>',
        ]);

        $result = $api->getAllAssignmentsWithItems();

        $this->assertCount(1, $result);
        $this->assertStringContainsString('<RefNo>1</RefNo>', $result[0]['assignment']);
        $this->assertStringContainsString('<Detail>x</Detail>', $result[0]['item']);
    }

    #[TestDox('logs and skips an assignment whose detail feed fails')]
    public function testSkipsAssignmentWithFailingItem(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning')->with($this->stringContains('Skipping Visma assignment B'));

        $api = $this->api([
            self::LIST_URL     => '<Assignments><Assignment><Guid>A</Guid></Assignment><Assignment><Guid>B</Guid></Assignment></Assignments>',
            'guidAssignment=A' => '<Assignments><Assignment><Guid>A</Guid></Assignment></Assignments>',
        ], $logger);

        $this->assertCount(1, $api->getAllAssignmentsWithItems());
    }

    #[TestDox('throws when assignments exist but none can be loaded')]
    public function testThrowsWhenNothingCanBeLoaded(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->api([self::LIST_URL => '<Assignments><Assignment><Guid>A</Guid></Assignment></Assignments>'])
            ->getAllAssignmentsWithItems();
    }

    #[TestDox('returns an empty list for a feed without assignments')]
    public function testEmptyFeed(): void
    {
        $this->assertSame([], $this->api([self::LIST_URL => '<Assignments></Assignments>'])->getAllAssignmentsWithItems());
    }

    #[TestDox('throws when Visma reports an unknown group')]
    public function testUnknownGroupThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->api([self::LIST_URL => '<Error>Kunde inte hitta gruppen</Error>'])->getAllAssignmentsWithItems();
    }

    #[TestDox('throws when the list feed is empty')]
    public function testEmptyContentThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->api([self::LIST_URL => ''])->getAllAssignmentsWithItems();
    }

    #[TestDox('appends guidGroup to a list url that has none')]
    public function testAppendsGuidGroupToListUrl(): void
    {
        $requested = [];
        $api       = new class ('https://example.test/List.ashx', 'g1', $requested) extends VismaApi {
            public function __construct(string $url, string $group, private array &$requested)
            {
                parent::__construct($url, $group);
            }

            protected function fetchXml(string $url): string
            {
                $this->requested[] = $url;

                return '<Assignments></Assignments>';
            }
        };

        $api->getAllAssignmentsWithItems();

        $this->assertSame(['https://example.test/List.ashx?guidGroup=g1'], $requested);
    }

    #[TestDox('keeps a list url that already contains guidGroup')]
    public function testKeepsExistingGuidGroup(): void
    {
        $requested = [];
        $api       = new class ('https://example.test/List.ashx?guidGroup=g1', 'g1', $requested) extends VismaApi {
            public function __construct(string $url, string $group, private array &$requested)
            {
                parent::__construct($url, $group);
            }

            protected function fetchXml(string $url): string
            {
                $this->requested[] = $url;

                return '<Assignments></Assignments>';
            }
        };

        $api->getAllAssignmentsWithItems();

        $this->assertSame(['https://example.test/List.ashx?guidGroup=g1'], $requested);
    }

    #[TestDox('requires a list url and guid group')]
    public function testRequiresConfiguration(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new VismaApi('', '');
    }

    /**
     * Build an API whose feeds come from a map. A key matches when the requested URL contains it.
     *
     * @param array<string, string> $feeds
     */
    private function api(array $feeds, ?LoggerInterface $logger = null): VismaApi
    {
        return new class (self::LIST_URL, 'group', $feeds, $logger ?? $this->createStub(LoggerInterface::class)) extends VismaApi {
            public function __construct(string $url, string $group, private array $feeds, LoggerInterface $logger)
            {
                parent::__construct($url, $group, $logger);
            }

            protected function fetchXml(string $url): string
            {
                foreach ($this->feeds as $needle => $xml) {
                    if (str_contains($url, (string) $needle)) {
                        return $xml;
                    }
                }

                throw new \RuntimeException('HTTP 404');
            }
        };
    }
}
