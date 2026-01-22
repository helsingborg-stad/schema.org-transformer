<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\Tests\TestHelper;

#[CoversClass(MapOrganizer::class)]
final class MapOrganizerTest extends TestCase
{
    #[TestDox('event::organizer is constructed from taxonomies in _embedded.acf:term')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(new WPHeadlessEventTransform('hl')),
            '{
                "_embedded": {
                    "acf:term": [
                        {
                            "name": "Not an Organizer",
                            "taxonomy": "category",
                            "acf": {}
                        },
                        {
                            "name": "Test Organizer",
                            "taxonomy": "organization",
                            "acf": {
                                "address": "123 Organizer St, Organizer City, OR 12345",
                                "url": "https://organizer.example.com",
                                "email": "organizer@example.com",
                                "telephone": "123-456-7890",
                                "contact": "test contact"
                            }
                        },
                        {
                            "name": "Another Organizer",
                            "taxonomy": "organization",
                            "acf": {
                                "address": null,
                                "url": null,
                                "email": "another@example.com",
                                "telephone": null
                            }
                        }   
                    ]
                }
            }',
            Schema::event()->organizer([
                Schema::organization()
                    ->name('Test Organizer')
                    ->url('https://organizer.example.com')
                    ->telephone('123-456-7890')
                    ->email('organizer@example.com')
                    ->address('123 Organizer St, Organizer City, OR 12345')
                    ->contactPoint([Schema::contactPoint()
                        ->name('test contact')
                    ]),
                Schema::organization()
                    ->name('Another Organizer')
                    ->email('another@example.com')
                    ->contactPoint([]),
            ])
        );
    }

    #[TestDox('event::organizer([]) when missing _embedded.acf:term')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123
            }',
            Schema::event()->organizer([])
        );
    }
}
