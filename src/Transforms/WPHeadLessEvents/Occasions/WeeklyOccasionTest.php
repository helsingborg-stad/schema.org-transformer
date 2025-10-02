<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\WeeklyOccasion;
use DateTime;

#[CoversClass(WeeklyOccasion::class)]
final class WeeklyOccasionTest extends TestCase
{
    #[TestDox('getDatesInPeriod generates correct dates for weekly events')]
    public function testExpandWeeklyDates()
    {
        $events = WeeklyOccasion::getDatesInPeriod(
            new DateTime('2025-10-01'),
            new DateTime('2025-10-31'),
            2, // tuesdays
            1  // every week
        );
        $this->assertEquals(
            [
            new DateTime('2025-10-07'),
            new DateTime('2025-10-14'),
            new DateTime('2025-10-21'),
            new DateTime('2025-10-28')
            ],
            $events
        );
    }

    #[TestDox('getDatesInPeriod generates correct dates for weekly events over new years eve')]
    public function testExpandWeeklyDatesOverNewYear()
    {
        $events = WeeklyOccasion::getDatesInPeriod(
            new DateTime('2025-12-27'),
            new DateTime('2026-02-07'),
            6, // saturdays
            2  // every 2:nd week
        );
        $this->assertEquals(
            [
            new DateTime('2025-12-27'),
            new DateTime('2026-01-10'),
            new DateTime('2026-01-24'),
            new DateTime('2026-02-07')
            ],
            $events
        );
    }

    #[TestDox('getDatesInPeriod tolerates start and end dates that are not on the correct weekday')]
    public function testExpandWeeklyDatesStartAndEndAreWrongWeekdays()
    {
        $events = WeeklyOccasion::getDatesInPeriod(
            new DateTime('2025-10-01'),
            new DateTime('2025-10-31'),
            4, // thursdays
            1  // every week
        );
        $this->assertEquals(
            [
                new DateTime('2025-10-02'),
                new DateTime('2025-10-09'),
                new DateTime('2025-10-16'),
                new DateTime('2025-10-23'),
                new DateTime('2025-10-30')
            ],
            $events
        );
    }
}
