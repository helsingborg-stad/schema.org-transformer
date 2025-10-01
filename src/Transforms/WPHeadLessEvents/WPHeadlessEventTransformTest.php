<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\WPHeadlessEventTransform;

#[CoversClass(WPHeadlessEventTransform::class)]
final class WPHeadlessEventTransformTest extends TestCase
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
            ->identifier("HL123")
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
            // ->url(null)
            ->setProperty('x-created-by', 'municipio://schema.org-transformer/wp-headless');

        $actualEvent = (new WPHeadlessEventTransform('HL'))->transform(
            [$source]
        )[0];

        $this->assertEquals(
            $expectedEvent->toArray(),
            $actualEvent
        );
    }
}
