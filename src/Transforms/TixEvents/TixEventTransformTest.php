<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\TixEvents\TixEventTransform;
use Municipio\Schema\Schema;

#[CoversClass(ElementarySchoolTransform::class)]
final class TixEventTransformTest extends TestCase
{
    private function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    #[TestDox('it doesn\'t break when a lot is missing')]
    public function testAlmostNoSourceData()
    {
        $source        = $this->prepareJsonForTransform('{
            "EventGroupId": 123
            }');
        $expectedEvent = Schema::event()
            ->identifier("tix_123")
            ->image([])
            ->eventSchedule([]);

        $actualEvent = (new TixEventTransform('tix_'))->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent
        );
    }

    #[TestDox('event::image is extracted from source')]
    public function testImagesAreTransformedCorrectly()
    {
        $source = $this->prepareJsonForTransform('{
            "EventGroupId": 123,
            "SubTitle": "Event title that goes into images",
            "HasFeaturedImage": true,
            "FeaturedImagePath": "https://example.com/image1.jpg"
        }');

        $expectedEvent = Schema::event()
            ->image([
                Schema::imageObject()
                    ->url('https://example.com/image1.jpg')
                    ->name('Event title that goes into images')
                    ->description('Event title that goes into images')
                    ->caption('Event title that goes into images')
            ]);

        $actualEvent = (new TixEventTransform('tix_'))->transformImages(
            Schema::event(),
            $source
        );

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent->toArray()
        );
    }

    #[TestDox('event::eventSchedule is extracted from source')]
    public function testTRansformEventSchedule()
    {
        $source = $this->prepareJsonForTransform('{
            "EventGroupId": 123,
            "Dates": [
                {
                    "EventId": 1,
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-01T18:00:00+02:00",
                    "EndDate": "2024-10-01T20:00:00+02:00"
                },
                {
                    "NOTE": "This event should be ignored since it belongs to another group",
                    "EventId": 2,
                    "DefaultEventGroupId": 555,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                },
                {
                    "NOTE": "This event should be ignored since it has no EventId",
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                },
                {
                    "EventId": 10,
                    "DefaultEventGroupId": 123,
                    "StartDate": "2024-10-15T18:00:00+02:00",
                    "EndDate": "2024-10-15T20:00:00+02:00"
                }
            ]
        }');

        $expectedEvent = Schema::event()
            ->eventSchedule([
                Schema::schedule()
                    ->identifier('tix_123_1')
                    ->startDate('2024-10-01T18:00:00+02:00')
                    ->endDate('2024-10-01T20:00:00+02:00'),
                Schema::schedule()
                    ->identifier('tix_123_10')
                    ->startDate('2024-10-15T18:00:00+02:00')
                    ->endDate('2024-10-15T20:00:00+02:00'),
            ]);

        $actualEvent = (new TixEventTransform('tix_'))->transformEventSchedule(
            Schema::event(),
            $source
        );

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent->toArray()
        );
    }

    #[TestDox('event::place is guessed from first occurence in source -> Dates')]
    public function testTransformPlace()
    {
        $source = $this->prepareJsonForTransform('{
            "EventGroupId": 123,
            "Dates": [
                {
                    "EventId": 1,
                    "DefaultEventGroupId": 123,
                    "Venue": "Rådhuset",
                    "Hall": "Rådssalen"
                },
                {
                    "EventId": 2,
                    "DefaultEventGroupId": 123,
                    "Venue": "Kulturhuset",
                    "Hall": "Stora salen"
                }
            ]
        }');

        $expectedEvent = Schema::event()
            ->place(
                Schema::place()
                    ->name('Rådhuset')
                    ->description('Rådssalen')
            );

        $actualEvent = (new TixEventTransform('tix_'))->transformPlace(
            Schema::event(),
            $source
        );

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent->toArray()
        );
    }
}
