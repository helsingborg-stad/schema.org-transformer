<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPLegacyEventTransform;

final class WPLegacyEventTransformTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $transformer = new WPLegacyEventTransform('idprefix');
        $this->assertInstanceOf(WPLegacyEventTransform::class, $transformer);
    }
}
