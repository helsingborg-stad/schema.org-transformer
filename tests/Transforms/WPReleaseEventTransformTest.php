<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

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

    #[TestDox('id is set from the idprefix and the id in the data')]
    public function testIdIsSetFromIdPrefixAndIdInData(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertEquals('idprefix5', $events[0]['@id']);
    }

    #[TestDox('skips event if id is not set')]
    public function testSkipsEventIfIdIsNotSet(): void
    {
        $events = $this->transformer->transform([$this->getRow(['id' => null])]);
        $this->assertEmpty($events);
    }

    #[Testdox('sets title from the title in the data')]
    public function testSetsTitleFromTitleInData(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertEquals('Test Event', $events[0]['name']);
    }

    #[TestDox('sets image from embedded featured media')]
    public function testSetsImageFromFeaturedMediaWhenAvailableAsUrl(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);

        $this->assertEquals('http://localhost:8444/wp-content/uploads/2025/02/521-600x400-1.jpg', $events[0]['image']['url']);
        $this->assertEquals('Test Description', $events[0]['image']['description']);
    }

    #[TestDox('sets typicalAgeRange from the row data if available')]
    public function testSetsTypicalAgeRangeFromRowDataIfAvailable(): void
    {
        $events = $this->transformer->transform([$this->getRow(['acf' => [
            'age_restriction'      => true,
            'age_restriction_info' => '13-'
        ]])]);
        $this->assertEquals('13-', $events[0]['typicalAgeRange']);
    }

    #[TestDox('sets location from the row data if available')]
    public function testSetsLocationFromRowDataIfAvailable(): void
    {
        $events = $this->transformer->transform([$this->getRow([
            'acf' => [
                "physical_virtual" => "physical",
                "location_name"    => "Test Location",
                "location"         => [
                "lat"     => 56.0473078,
                "lng"     => 12.6921272,
                "address" => "Drottninggatan 14, 252 21 Helsingborg, Sverige",
                ]
            ]
        ])]);

        $this->assertEquals('Test Location', $events[0]['location']['name']);
        $this->assertEquals(56.0473078, $events[0]['location']['latitude']);
        $this->assertEquals(12.6921272, $events[0]['location']['longitude']);
        $this->assertEquals('Drottninggatan 14, 252 21 Helsingborg, Sverige', $events[0]['location']['address']);
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
