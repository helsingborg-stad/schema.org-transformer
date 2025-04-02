<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;

class ApplyUrlTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(ApplyUrl::class, new ApplyUrl());
    }

    #[TestDox('applies url if available')]
    public function testApplyUrl(): void
    {
        $applyUrl = new ApplyUrl();

        $event = $applyUrl->apply(Schema::event(), ['event_link' => "https://example.com"]);

        $this->assertEquals("https://example.com", $event->getProperty('url'));
    }
}
