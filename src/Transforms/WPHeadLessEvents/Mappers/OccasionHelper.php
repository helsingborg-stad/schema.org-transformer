<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use DateTime;

class OccasionHelper
{
    public static function tryMapDatesAndTimes(array $collection, string $dateKey, string $timeKey): array
    {
        return array_filter(
            array_map(
                fn ($d) =>
                    $d[$dateKey] ?? null
                    ? self::tryMapDateTime($d[$dateKey] ?? '', $d[$timeKey] ?? '00:00:00')
                    : null,
                $collection
            )
        );
        return $dates;
    }

    public static function tryMapDate(string $date): ?string
    {
        try {
            return (new DateTime($date))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    public static function tryMapDateTime(string $date, string $time): ?string
    {
        try {
            $d = new DateTime($date);
            $t = DateTime::createFromFormat('H:i:s', $time);
            if ($d && $t) {
                return (new DateTime())
                    ->setDate((int)$d->format('Y'), (int)$d->format('m'), (int)$d->format('d'))
                    ->setTime((int)$t->format('H'), (int)$t->format('i'), (int)$t->format('s'))
                    ->format('c');
            }
        } catch (\Exception) {
            return null;
        }

        if (!preg_match('/^\d{8}$/', $date)) {
            return null;
        }
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return null;
        }

        $year  = (int)substr($date, 0, 4);
        $month = (int)substr($date, 4, 2);
        $day   = (int)substr($date, 6, 2);
        if (!checkdate($month, $day, $year)) {
            return null;
        }

        $hour   = (int)substr($time, 0, 2);
        $minute = (int)substr($time, 3, 2);
        $second = (int)substr($time, 6, 2);
        if (!($hour >= 0 && $hour <= 23 && $minute >= 0 && $minute <= 59 && $second >= 0 && $second <= 59)) {
            return null;
        }
        return (new DateTime())
            ->setDate($year, $month, $day)
            ->setTime($hour, $minute, $second)
            ->format('c');
    }
}
