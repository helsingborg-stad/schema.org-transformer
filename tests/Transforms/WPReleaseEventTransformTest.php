<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPReleaseEventTransform;

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

    #[TestDox('returns an array of Event objects')]
    public function testTransformReturnsArrayOfEventObjects(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);

        $this->assertIsArray($events);
        $this->assertCount(1, $events);
        $this->assertEquals($events[0]['@type'], 'Event');
    }

    #[TestDox('creates one event for every occasion')]
    public function testCreatesOneEventForEachOccasion(): void
    {
        $row                     = $this->getRow();
        $row['acf']['occasions'] = [
            [ "date" => "20250606", "startTime" => "12:00:00", "endTime" => "13:00:00" ],
            [ "date" => "20250607", "startTime" => "12:00:00", "endTime" => "13:00:00"]
        ];

        $events = $this->transformer->transform([$row]);

        $this->assertCount(2, $events);
    }

    #[TestDox('id is set from the idprefix and the id in the data')]
    public function testIdIsSetFromIdPrefixAndIdInData(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertEquals('idprefix5', $events[0]['@id']);
    }

    #[TestDox('skips event if id is not set')]
    public function testSkipsEventIfIdIsNotSet(): void
    {
        $row = $this->getRow();
        unset($row['id']);

        $events = $this->transformer->transform([$row]);

        $this->assertEmpty($events);
    }

    #[TestDox('skips event if title is not set')]
    public function testSkipsEventIfTitleIsNotSet(): void
    {
        $row = $this->getRow();
        unset($row['title']);

        $events = $this->transformer->transform([$row]);

        $this->assertEmpty($events);
    }

    #[Testdox('sets title from the title in the data')]
    public function testSetsTitleFromTitleInData(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertEquals('Test Event', $events[0]['name']);
    }

    #[TestDox('sets description from the description in the data if available')]
    public function testSetsDescriptionFromDescriptionInDataIfAvailable(): void
    {
        $row                       = $this->getRow();
        $row['acf']['description'] = 'Test Description';

        $events = $this->transformer->transform([$row]);

        $this->assertEquals('Test Description', $events[0]['description']);
    }

    #[TestDox('sets image from embedded featured media')]
    public function testSetsImageFromFeaturedMediaWhenAvailableAsUrl(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);

        $this->assertEquals('http://localhost:8444/wp-content/uploads/2025/02/521-600x400-1.jpg', $events[0]['image']['url']);
        $this->assertEquals('Test Description', $events[0]['image']['description']);
    }

    #[TestDox('does not set image if source_url is not available')]
    public function testDoesNotSetImageIfSourceUrlIsNotAvailable(): void
    {
        $row = $this->getRow();
        unset($row['_embedded']['wp:featuredmedia'][0]['source_url']);
        $events = $this->transformer->transform([$row]);

        $this->assertArrayNotHasKey('image', $events[0]);
    }

    #[TestDox('sets typicalAgeRange from the row data if available')]
    public function testSetsTypicalAgeRangeFromRowDataIfAvailable(): void
    {
        $row                                = $this->getRow();
        $row['acf']['age_restriction']      = true;
        $row['acf']['age_restriction_info'] = '13-';

        $events = $this->transformer->transform([$row]);

        $this->assertEquals('13-', $events[0]['typicalAgeRange']);
    }

    #[TestDox('sets location from the row data if available')]
    public function testSetsLocationFromRowDataIfAvailable(): void
    {
        $row                            = $this->getRow();
        $row['acf']['physical_virtual'] = 'physical';
        $row['acf']['location_name']    = 'Test Location';
        $row['acf']['location']         = [
            'lat'     => 56.0473078,
            'lng'     => 12.6921272,
            'address' => 'Drottninggatan 14, 252 21 Helsingborg, Sverige',
        ];

        $events = $this->transformer->transform([$row]);

        $this->assertEquals('Test Location', $events[0]['location']['name']);
        $this->assertEquals(56.0473078, $events[0]['location']['latitude']);
        $this->assertEquals(12.6921272, $events[0]['location']['longitude']);
        $this->assertEquals('Drottninggatan 14, 252 21 Helsingborg, Sverige', $events[0]['location']['address']);
    }

    #[TestDox('sets schema type from event type in the row data')]
    #[DataProvider('eventTypeProvider')]
    public function testSetsSchemaTypeFromEventTypeInRowData($type): void
    {
        $row                = $this->getRow();
        $row['acf']['type'] = $type;
        $events             = $this->transformer->transform([$row]);
        $this->assertEquals($type, $events[0]['@type']);
    }

    public static function eventTypeProvider(): array
    {
        return [
            'BusinessEvent'    => ['BusinessEvent'],
            'ChildrensEvent'   => ['ChildrensEvent'],
            'ComedyEvent'      => ['ComedyEvent'],
            'DanceEvent'       => ['DanceEvent'],
            'DeliveryEvent'    => ['DeliveryEvent'],
            'EducationEvent'   => ['EducationEvent'],
            'EventSeries'      => ['EventSeries'],
            'ExhibitionEvent'  => ['ExhibitionEvent'],
            'Festival'         => ['Festival'],
            'FoodEvent'        => ['FoodEvent'],
            'Hackathon'        => ['Hackathon'],
            'LiteraryEvent'    => ['LiteraryEvent'],
            'MusicEvent'       => ['MusicEvent'],
            'PublicationEvent' => ['PublicationEvent'],
            'SaleEvent'        => ['SaleEvent'],
            'ScreeningEvent'   => ['ScreeningEvent'],
            'SocialEvent'      => ['SocialEvent'],
            'SportsEvent'      => ['SportsEvent'],
            'TheaterEvent'     => ['TheaterEvent'],
            'VisualArtsEvent'  => ['VisualArtsEvent'],

        ];
    }

    #[TestDox('sets offers from the row data if available')]
    public function testSetsOffersFromRowDataIfAvailable(): void
    {
        $row                      = $this->getRow();
        $row['acf']['pricing']    = 'expense';
        $row['acf']['pricesList'] = [
            [
                'price'      => '100',
                'priceLabel' => 'Standard ticket',
            ]
        ];

        $events = $this->transformer->transform([$row]);

        $this->assertEquals('Offer', $events[0]['offers'][0]['@type']);
        $this->assertEquals('100', $events[0]['offers'][0]['price']);
        $this->assertEquals('SEK', $events[0]['offers'][0]['priceCurrency']);
        $this->assertEquals('Standard ticket', $events[0]['offers'][0]['name']);
    }

    #[TestDox('sets isAccessibleForFree to true if pricing is "free"')]
    public function testSetsIsAccessibleForFreeToTrueIfPricingIsNotExpense(): void
    {
        $row                   = $this->getRow();
        $row['acf']['pricing'] = 'free';
        $events                = $this->transformer->transform([$row]);
        $this->assertTrue($events[0]['isAccessibleForFree']);
    }

    #[TestDox('sets the event status to any of the supported types if available')]
    public function testSetsEventStatusToAnyOfTheSupportedTypesIfAvailable(): void
    {
        $row                       = $this->getRow();
        $row['acf']['eventStatus'] = 'https://schema.org/EventCancelled';
        $events                    = $this->transformer->transform([$row]);
        $this->assertEquals('https://schema.org/EventCancelled', $events[0]['eventStatus']);
    }

    /**
     * Get a row of data
     *
     * @param array $data Additional data to merge with the rows default data
     * @return array A single row of data
     */
    private function getRow(array $data = []): array
    {
        $json    = file_get_contents(__DIR__ . '/../fixtures/wp-release-event-row.json');
        $fixture = json_decode($json, true);

        return array_merge($fixture, $data);
    }
}
