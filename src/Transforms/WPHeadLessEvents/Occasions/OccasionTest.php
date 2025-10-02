<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\Occasion;
use DateTime;
use stdClass;

#[CoversClass(Occasion::class)]
final class OccasionTest extends TestCase
{
    #[DataProvider('sampleInvalidDates')]
    public function testTryParseDateWithInvalidDate($date)
    {
        $this->assertNull(Occasion::tryParseDate($date));
    }

    #[DataProvider('sampleDates20251017')]
    public function testTryParseDateWithValidDate($date)
    {
        $this->assertEquals(
            new DateTime('2025-10-17'),
            Occasion::tryParseDate($date)
        );
    }

    #[DataProvider('sampleRecords')]
    public function testTryMapRecord($record, $expected)
    {
        $this->assertEquals(
            $expected,
            Occasion::tryMapRecord($record, 'd', 't')
        );
    }

    public static function sampleRecords(): iterable
    {
        return [
            [
                ['d' => '2025-10-17','t' => '13:37:00'],
                new DateTime('2025-10-17 13:37:00')
            ]
        ];
    }

    public static function sampleDates20251017(): iterable
    {
        return [
            ['2025-10-17'],
            ['20251017']
        ];
    }

    public static function sampleInvalidDates(): iterable
    {
        return[
            [null],
            ['invalid-date'],
            [['an' => 'object']],
            [new stdClass()]
        ];
    }
}
