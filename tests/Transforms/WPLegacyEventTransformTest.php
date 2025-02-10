<?php

declare(strict_types=1);

namespace SchemaTransformer\Tests\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPLegacyEventTransform;

final class WPLegacyEventTransformTest extends TestCase
{
    private WPLegacyEventTransform $transformer;

    protected function setUp(): void
    {
        $pathValueAccessor = new \SchemaTransformer\Util\ArrayPathResolver();
        $this->transformer = new WPLegacyEventTransform(
            'idprefix',
            new \SchemaTransformer\Transforms\SplitRowsByOccasion('occasions'),
            new \SchemaTransformer\Transforms\WPLegacyEventTransform\EventFactory(),
            [
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyName('title.rendered', $pathValueAccessor),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyDescription('content.rendered', $pathValueAccessor),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyLocationPlace('location.title', 'location.formatted_address', 'location.latitude', 'location.longitude', $pathValueAccessor),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyStartDate(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEndDate(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventStatus(),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyImage(),
                new \SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators\ApplyKeywords(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventAttendanceMode(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOrganizer(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyTypicalAgeRange(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOffers(),
            ],
            new \SchemaTransformer\Transforms\Validators\EventValidator()
        );
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(WPLegacyEventTransform::class, $this->transformer);
    }

    #[TestDox('transform method returns array')]
    public function testTransformMethodReturnsArray(): void
    {
        $events = $this->transformer->transform([$this->getRow()]);
        $this->assertIsArray($events);
    }

    #[TestDox('transform method returns array with 3 events when given one event with 3 occasions')]
    public function testTransformMethodReturnsArrayWithThreeEventsWhenGivenOneEventWithThreeOccasions(): void
    {
        $row    = $this->getRow();
        $events = $this->transformer->transform([$row]);
        $this->assertCount(3, $events);
    }

    /**
     * Get a row of data
     *
     * @param array $data Additional data to merge with the rows default data
     * @return array A single row of data
     */
    private function getRow(array $data = []): array
    {
        $json    = file_get_contents(__DIR__ . '/../fixtures/wp-legacy-event.json');
        $fixture = json_decode($json, true);

        return array_merge($fixture, $data);
    }
}
