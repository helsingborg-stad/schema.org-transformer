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
    #[TestDox('event::organizer is constructed from acf.organizerName, acf.organizerPhone, acf.organizerEmail, acf.organizerAddress, acf.organizerUrl')]
    public function testItWorks()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(new WPHeadlessEventTransform('hl')),
            '{
                "acf": {
                    "organizerName": "Test Organizer",
                    "organizerPhone": "123-456-7890",
                    "organizerEmail": "organizer@example.com",
                    "organizerAddress": "123 Organizer St, Organizer City, OR 12345",
                    "organizerUrl": "https://organizer.example.com"
                }
            }',
            Schema::event()->organizer([Schema::organization()
                ->name('Test Organizer')
                ->telephone('123-456-7890')
                ->email('organizer@example.com')
                ->address('123 Organizer St, Organizer City, OR 12345')
            ->url('https://organizer.example.com')])
        );
    }

    #[TestDox('event::organizer[] when missing acf.organizerName')]
    public function testMissing()
    {
        (new TestHelper())->expectMapperToConvertSourceTo(
            new MapOrganizer(new WPHeadlessEventTransform('hl')),
            '{
                "id": 123,
                "organizerUrl": "never considered"

            }',
            Schema::event()->organizer([])
        );
    }
}
