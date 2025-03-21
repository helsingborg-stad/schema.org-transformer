<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPLegacyEventTransform;
use Spatie\Snapshots\MatchesSnapshots;

#[CoversClass(WPLegacyEventTransform::class)]
final class WPLegacyEventTransformTest extends TestCase
{
    use MatchesSnapshots;

    private WPLegacyEventTransform $transformer;

    protected function setUp(): void
    {
        $idPrefix          = 'idprefix';
        $pathValueAccessor = new \SchemaTransformer\Util\ArrayPathResolver();
        $this->transformer = new WPLegacyEventTransform(
            $idPrefix,
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
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyKeywords(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventAttendanceMode(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyEventSeries(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOrganizer(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyPhysicalAccessibilityFeatures(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyTypicalAgeRange(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyOffers(),
                new \SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators\ApplyUrl()
            ],
            new \SchemaTransformer\Transforms\Validators\EventValidator()
        );
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeCreated(): void
    {
        $this->assertInstanceOf(WPLegacyEventTransform::class, $this->transformer);
    }

    #[TestDox('matches snapshot')]
    public function testMatchesSnapshot(): void
    {
        $events   = $this->transformer->transform([$this->getRow()]);
        $snapshot = json_encode($events, JSON_PRETTY_PRINT);
        $this->assertMatchesJsonSnapshot($snapshot);
    }

    /**
     * Get a row of data
     *
     * @param array $data Additional data to merge with the rows default data
     * @return array A single row of data
     */
    private function getRow(array $data = []): array
    {
        $json    = file_get_contents(__DIR__ . '/../../tests/fixtures/wp-legacy-event.json');
        $fixture = json_decode($json, true);

        return array_merge($fixture, $data);
    }
}
