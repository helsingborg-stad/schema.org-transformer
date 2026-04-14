<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Event\AxiellEvents\AxiellEventTransform;

#[CoversClass(AxiellEventTransform::class)]
final class AxiellEventTransformTest extends TestCase
{
    private function prepareJsonForTransform($json)
    {
        return json_decode($json, true);
    }

    #[TestDox('it doesn\'t break when a lot is missing')]
    public function testAlmostNoSourceData()
    {
        $source        = $this->prepareJsonForTransform('{
            "id": 123
            }');
        $expectedEvent = Schema::event()
            ->identifier("ax-123")
            ->name(null)
            ->description([])
            ->isAccessibleForFree(true)
            ->eventAttendanceMode(Schema::eventAttendanceModeEnumeration()::OfflineEventAttendanceMode)
            ->startDate(null)
            ->endDate(null)
            ->organizer([])
            ->location([])
            ->image([])
            ->eventSchedule([])
            ->offers([])
            ->eventStatus(Schema::eventStatusType()::EventScheduled)
            ->keywords([])
            ->physicalAccessibilityFeatures([])
            ->typicalAgeRange(null)
            ->url('https://example.com/event/123')
            ->potentialAction([])
            ->setProperty('x-created-by', 'municipio://schema.org-transformer/axiell-events');

        $actualEvent = (new AxiellEventTransform('ax-', 'https://example.com/event/'))->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent
        );
    }
}
