<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\DayOfWeek;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class WPExhibitionEventTransformTest extends TestCase
{
    private string $fixturePath = __DIR__ . '/../../tests/fixtures/wp-exhibition-event.json';

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $this->assertInstanceOf(WPExhibitionEventTransform::class, new WPExhibitionEventTransform());
    }

    #[TestDox('fixture file exists')]
    public function testFixtureExists(): void
    {
        $this->assertFileExists($this->fixturePath);
    }

    #[TestDox('fixture file is transformed to json')]
    public function testFixtureFileIsTransformed(): void
    {
        $json = $this->getFixtureAsJson();

        $this->assertNotEmpty($json);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('id', $json);
    }

    #[TestDox('data is transformed to ExhibitionEvent')]
    public function testDataIsTransformedToExhibitionEvent(): void
    {
        $result = $this->getTransformedResult();

        $this->assertEquals('ExhibitionEvent', $result['@type']);
    }

    #[TestDox('name is set')]
    public function testResultContainsName(): void
    {
        $result = $this->getTransformedResult();

        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('Testutställning', $result['name']);
    }

    #[TestDox('description is set')]
    public function testResultContainsDescription(): void
    {
        $result = $this->getTransformedResult();

        $this->assertArrayHasKey('description', $result);
        $this->assertStringStartsWith('Lorem ipsum dolor sit amet', $result['description']);
    }

    #[TestDox('organizer is set')]
    public function testResultContainsOrganizer(): void
    {
        $result = $this->getTransformedResult();

        $this->assertArrayHasKey('organizer', $result);
        $this->assertEquals('Testorganizer', $result['organizer']['name']);
    }

    #[TestDox('location.openingHoursSpecification is set')]
    public function testResultContainsOpeningHoursSpecification(): void
    {
        $result       = $this->getTransformedResult();
        $openingHours = $result['location']['openingHoursSpecification'];

        // Open 09:00-18:00 Monday-Saturday
        $this->assertContains(DayOfWeek::Monday, $openingHours[0]['dayOfWeek']);
        $this->assertContains(DayOfWeek::Tuesday, $openingHours[0]['dayOfWeek']);
        $this->assertContains(DayOfWeek::Wednesday, $openingHours[0]['dayOfWeek']);
        $this->assertContains(DayOfWeek::Thursday, $openingHours[0]['dayOfWeek']);
        $this->assertContains(DayOfWeek::Friday, $openingHours[0]['dayOfWeek']);
        $this->assertContains(DayOfWeek::Saturday, $openingHours[0]['dayOfWeek']);
        $this->assertNotContains(DayOfWeek::Sunday, $openingHours[0]['dayOfWeek']);
        $this->assertEquals('09:00:00', $openingHours[0]['opens']);
        $this->assertEquals('18:00:00', $openingHours[0]['closes']);

        // Closed on sundays
        $this->assertEquals([DayOfWeek::Sunday], $openingHours[1]['dayOfWeek']);
        $this->assertArrayNotHasKey('opens', $openingHours[1]);
        $this->assertArrayNotHasKey('closes', $openingHours[1]);
    }

    #[TestDox('location.name is set')]
    public function testResultContainsLocationName(): void
    {
        $result = $this->getTransformedResult();

        $this->assertArrayHasKey('location', $result);
        $this->assertArrayHasKey('name', $result['location']);
        $this->assertEquals('Testlocation', $result['location']['name']);
    }

    #[TestDox('location geo data is set')]
    public function testResultContainsLocationGeoData(): void
    {
        $result = $this->getTransformedResult();

        $this->assertEquals('Dunkers kulturhus, Kungsgatan, Helsingborg, Sverige', $result['location']['address']);
        $this->assertEquals(56.0478422, $result['location']['latitude']);
        $this->assertEquals(12.6890694, $result['location']['longitude']);
    }

    #[TestDox('startDate and endDate is set')]
    public function testResultContainsStartDateAndEndDate(): void
    {
        $result = $this->getTransformedResult();

        $this->assertArrayHasKey('startDate', $result);
        $this->assertEquals('2025-08-01', $result['startDate']);

        $this->assertArrayHasKey('endDate', $result);
        $this->assertEquals('2025-08-31', $result['endDate']);
    }

    #[TestDox('offers is set')]
    public function testResultContainsOffers(): void
    {
        $result = $this->getTransformedResult();

        $this->assertEquals('Vuxen', $result['offers'][0]['name']);
        $this->assertEquals(120, $result['offers'][0]['price']);
        $this->assertEquals('SEK', $result['offers'][0]['priceCurrency']);

        $this->assertEquals('Barn och unga till och med 18', $result['offers'][1]['name']);
        $this->assertEquals(0, $result['offers'][1]['price']);

        $this->assertEquals('Kulturkortet', $result['offers'][2]['name']);
        $this->assertEquals(0, $result['offers'][2]['price']);
    }

    #[TestDox('image is set')]
    public function testResultContainsImage(): void
    {
        $result = $this->getTransformedResult();

        $this->assertCount(2, $result['image']);

        $this->assertEquals('https://source.api/wp-content/uploads/sites/6/2025/08/manuelthelensman-OeAp5Q-IYsY-unsplash-scaled.jpg', $result['image'][0]['url']);
        $this->assertEquals('Test alt', $result['image'][0]['description']);
        $this->assertEquals('manuelthelensman-OeAp5Q-IYsY-unsplash', $result['image'][0]['name']);

        $this->assertEquals('https://source.api/wp-content/uploads/sites/6/2025/08/871-1920x1024-1.jpg', $result['image'][1]['url']);
        $this->assertEquals('Test alt', $result['image'][1]['description']);
        $this->assertEquals('Picsum ID: 871', $result['image'][1]['name']);
    }

    private function getTransformedResult(): array
    {
        $transform = new WPExhibitionEventTransform();
        return $transform->transform([$this->getFixtureAsJson()])[0];
    }

    private function getFixtureAsJson(): array
    {
        return json_decode(file_get_contents($this->fixturePath), true);
    }
}
