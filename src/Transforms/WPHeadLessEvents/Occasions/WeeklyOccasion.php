<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Occasions;

use DateTime;
use DateInterval;
use DatePeriod;

class WeeklyOccasion extends Occasion
{
    public static array $weekdayNames = [
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday',
                7 => 'sunday',
    ];

    public static function getDatesInPeriod(
        DateTime $start, // assumed date only
        DateTime $end, // assumed date only
        int $dayOfWeek,
        int $weekInterval = 1
    ): array {
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            return [];
        }
        // adjust start to next occurence of weekday
        while ((int)$start->format('N') !== $dayOfWeek) {
            $start->modify('next ' . self::$weekdayNames[$dayOfWeek]);
        }
        $data     = [];
        $interval = new DateInterval('P' . $weekInterval . 'W');
        $period   = new DatePeriod($start, $interval, $end, DatePeriod::INCLUDE_END_DATE);
        foreach ($period as $date) {
            $data[] = (clone $date);
        }
        return $data;
    }
}
