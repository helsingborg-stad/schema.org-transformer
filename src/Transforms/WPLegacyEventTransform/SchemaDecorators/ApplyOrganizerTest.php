<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyOrganizerTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $decorator = new ApplyOrganizer();
        $this->assertInstanceOf(SchemaDecorator::class, $decorator);
    }

    #[TestDox('sets organizer properties')]
    public function testSetsOrganizerProperties()
    {
        $decorator = new ApplyOrganizer();

        $event = $decorator->apply(Schema::event(), $this->getTestData());

        $this->assertCount(2, $event->getProperty('organizer'));
        $this->assertEquals('Test organizer', $event->getProperty('organizer')[0]->getProperty('name'));
        $this->assertEquals('https://test-organizer-website.com', $event->getProperty('organizer')[0]->getProperty('url'));
        $this->assertEquals('organizer@example.com', $event->getProperty('organizer')[0]->getProperty('email'));
        $this->assertEquals('234-567 89 01', $event->getProperty('organizer')[0]->getProperty('telephone'));
        $this->assertEquals('Test organizer 2', $event->getProperty('organizer')[1]->getProperty('name'));
        $this->assertEquals('https://test-organizer2-website.com', $event->getProperty('organizer')[1]->getProperty('url'));
        $this->assertEquals('organizer2@example.com', $event->getProperty('organizer')[1]->getProperty('email'));
        $this->assertEquals('234-567 89 01', $event->getProperty('organizer')[1]->getProperty('telephone'));
    }

    private function getTestData(): array
    {
        return [
            "_embedded" => [
                "organizers" => [
                    [
                        "title"   => [
                        "rendered"   => "Test organizer",
                        "plain_text" => "Test organizer"
                        ],
                        "phone"   => "234-567 89 01",
                        "email"   => "organizer@example.com",
                        "website" => "https://test-organizer-website.com"
                    ],
                    [
                        "title"   => [
                        "rendered"   => "Test organizer 2",
                        "plain_text" => "Test organizer 2"
                        ],
                        "phone"   => "234-567 89 01",
                        "email"   => "organizer2@example.com",
                        "website" => "https://test-organizer2-website.com"
                    ]
                ]
            ]
        ];
    }
}
