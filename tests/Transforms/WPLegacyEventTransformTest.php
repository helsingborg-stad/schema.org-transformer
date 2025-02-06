<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPLegacyEventTransform;

final class WPLegacyEventTransformTest extends TestCase
{
    private WPLegacyEventTransform $transformer;

    protected function setUp(): void
    {
        $this->transformer = new WPLegacyEventTransform('idprefix');
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(WPLegacyEventTransform::class, $this->transformer);
    }

    #[TestDox('transform method returns array')]
    public function testTransformMethodReturnsArray(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertIsArray($events);
    }

    /**
     * Get a row of data
     *
     * @param array $data Additional data to merge with the rows default data
     * @return array A single row of data
     */
    private function getRow(array $data = []): array
    {
        $json    = file_get_contents(__DIR__ . '/../fixtures/wp-legacy-event.json');
        $fixture = json_decode($json, true);

        return array_merge($fixture, $data);
    }
}
