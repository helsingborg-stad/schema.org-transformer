<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\MonthlyOccasion;
use DateTime;

#[CoversClass(MonthlyOccasion::class)]
final class MonthlyOccasionTest extends TestCase
{
    #[TestDox('getDatesInPeriod generates correct dates for monthly events')]
    public function testGetDatesInPeriod()
    {
        $events = MonthlyOccasion::getDatesInPeriod(
            new DateTime('2025-10-01'),
            new DateTime('2026-10-31'),
            7, // every 7:th
            2  // every second month
        );
        $this->assertEquals(
            [
            new DateTime('2025-10-07'),
            new DateTime('2025-12-07'),
            new DateTime('2026-02-07'),
            new DateTime('2026-04-07'),
            new DateTime('2026-06-07'),
            new DateTime('2026-08-07'),
            new DateTime('2026-10-07')
            ],
            $events
        );
    }

    #[TestDox('getDatesInPeriod tolerates dayOfMonth > 30 (broken test)')]
    public function testGetDatesInPeriodWithDayOfMonthGreaterThan30()
    {
        $events = MonthlyOccasion::getDatesInPeriod(
            new DateTime('2025-01-01'),
            new DateTime('2025-06-01'),
            31,
            1
        );
        $this->assertEquals(
            [
                new DateTime('2025-01-31'),
                new DateTime('2025-02-28'),
                new DateTime('2025-03-31'),
                new DateTime('2025-04-30'),
                new DateTime('2025-05-31')
            ],
            $events
        );
    }
}
