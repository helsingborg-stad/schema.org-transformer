<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\Tests\TestHelper;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapOrganizer;

#[CoversClass(MapOrganizer::class)]
final class MapOrganizerTest extends TestCase
{
    #[TestDox('event::organizer is mapped from organizer.rendered')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{
                "_embedded": {
                    "organizers": [
                    {
                        "title": {
                        "rendered": "Test organizer",
                        "plain_text": "Test organizer"
                        },
                        "phone": "123-456 78 90",
                        "email": "organizer@example.com",
                        "website": "https://test-organizer-website.com"
                    },
                    {
                        "title": {
                        "rendered": "Test organizer 2",
                        "plain_text": "Test organizer 2"
                        },
                        "phone": "234-567 89 01",
                        "email": "organizer2@example.com",
                        "website": "https://test-organizer2-website.com"
                    }
                    ]
                }   
            }',
            Schema::event()->organizer([
                Schema::organization()
                    ->name('Test organizer')
                    ->telephone('123-456 78 90')
                    ->email('organizer@example.com')
                    ->url('https://test-organizer-website.com'),
                Schema::organization()
                    ->name('Test organizer 2')
                    ->telephone('234-567 89 01')
                    ->email('organizer2@example.com')
                    ->url('https://test-organizer2-website.com')
            ])
        );
    }

    #[TestDox('event::organizer([]) when no organizers are present')]
    public function testHandlesMissingOrganizers()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(),
            '{"id": 123}',
            Schema::event()->organizer([])
        );
    }
}
