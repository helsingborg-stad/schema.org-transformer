<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NorticEventTransform::class)]
final class NorticEventTransformTest extends TestCase
{
    public function testTransformsSampleEventAndItsShows(): void
    {
        $source = json_decode(
            file_get_contents(__DIR__ . '/sample.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $result = (new NorticEventTransform('nortic_'))->transform($source);

        $this->assertCount(1, $result);
        $this->assertSame('nortic_123', $result[0]['@id']);
        $this->assertSame('Fuldans', $result[0]['name']);
        $this->assertSame(['Folk dansar och skrålar och har sköj'], $result[0]['description']);
        $this->assertSame('2026-11-27 19:00', $result[0]['startDate']);
        $this->assertSame('2026-11-29 15:00', $result[0]['endDate']);
        $this->assertSame('Balettakademien', $result[0]['organizer'][0]['name']);
        $this->assertSame('Balettakademiens teater', $result[0]['location'][0]['name']);
        $this->assertCount(4, $result[0]['eventSchedule']);
        $this->assertSame('https://example.com/ticket/event/123', $result[0]['url']);
        $this->assertSame('municipio://schema.org-transformer/nortic', $result[0]['x-created-by']);
    }

    public function testTransformsAnEventWithNoShows(): void
    {
        $result = (new NorticEventTransform('nortic_'))->transform([
            'id'    => 1,
            'title' => 'Event without show details',
        ]);

        $this->assertCount(1, $result);
        $this->assertSame('nortic_1', $result[0]['@id']);
        $this->assertSame('Event without show details', $result[0]['name']);
        $this->assertSame([], $result[0]['eventSchedule']);
        $this->assertSame([], $result[0]['offers']);
    }
}
