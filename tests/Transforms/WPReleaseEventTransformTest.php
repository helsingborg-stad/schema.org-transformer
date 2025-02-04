<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform;
use Spatie\SchemaOrg\Event;

final class WPReleaseEventTransformTest extends TestCase
{
    private WpReleaseEventTransform $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new WPReleaseEventTransform('idprefix');
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(WPReleaseEventTransform::class, $this->transformer);
    }

    #[TestDox('transform returns an array of Event objects')]
    public function testTransformReturnsArrayOfEventObjects(): void
    {
        $events = $this->transformer->transform([['id' => 1]]);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals($events[0]['@type'], 'Event');
    }
}
