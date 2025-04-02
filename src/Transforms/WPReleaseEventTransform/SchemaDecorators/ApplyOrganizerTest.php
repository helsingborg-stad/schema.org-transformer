<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyOrganizer;
use Municipio\Schema\Event;

class ApplyOrganizerTest extends TestCase
{
    private ApplyOrganizer $decorator;

    protected function setUp(): void
    {
        $this->decorator = new ApplyOrganizer();
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $this->assertInstanceOf(ApplyOrganizer::class, $this->decorator);
    }

    #[TestDox('organizer is added to schema if available in data')]
    public function testOrganizerIsAddedToSchemaIfAvailableInData()
    {
        $event = new Event();
        $data  = [
            '_embedded' => [
                'wp:term' => [
                    [
                        [
                            'name'     => 'Test Organization',
                            'taxonomy' => 'organization',
                            'acf'      => [
                                'url'       => 'https://testorganization.org',
                                'email'     => 'org@testorganization.org',
                                'telephone' => '1234567890',
                                'address'   => [
                                    'address' => '123 Test St',
                                    'lat'     => 56.123456,
                                    'lng'     => -123.123456,
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $event     = $this->decorator->apply($event, $data);
        $organizer = $event->getProperty('organizer');

        $this->assertEquals('Test Organization', $organizer->getProperty('name'));
        $this->assertEquals('https://testorganization.org', $organizer->getProperty('url'));
        $this->assertEquals('org@testorganization.org', $organizer->getProperty('email'));
        $this->assertEquals('1234567890', $organizer->getProperty('telephone'));
        $this->assertEquals('123 Test St', $organizer->getProperty('address'));
    }
}
