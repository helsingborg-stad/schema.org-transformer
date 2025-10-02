<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Occasions;

use DateTime;
use DateInterval;
use DatePeriod;

class MonthlyOccasion extends Occasion
{
    public static function getDatesInPeriod(
        DateTime $start, // assumed date only
        DateTime $end, // assumed date only
        int $dayOfMonth,
        int $monthInterval = 1
    ): array {
        if ($dayOfMonth < 1 || $dayOfMonth > 31) {
            return [];
        }
        // adjust start to next occurence of weekday
        while ((int)$start->format('d') !== $dayOfMonth) {
            $start->modify('next day');
        }
        $data     = [];
        $interval = new DateInterval('P' . $monthInterval . 'M');
        $period   = new DatePeriod($start, $interval, $end, DatePeriod::INCLUDE_END_DATE);
        foreach ($period as $date) {
            $data[] = (clone $date);
        }
        return $data;
    }
}
