<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Occasions;

use DateTime;
use DateMalformedStringException;
use Exception;
use TypeError;

class Occasion
{
    public static function tryParseDate($date): ?DateTime
    {
        try {
            return new DateTime($date);
        } catch (DateMalformedStringException | Exception | TypeError $e) {
            return null;
        }
    }

    public static function tryMapRecords(array $collection, string $dateKey, string $timeKey): array /* of DateTime */
    {
        return array_filter(
            array_map(
                fn ($d) =>
                    $d[$dateKey] ?? null
                    ? self::tryMapRecord($d[$dateKey] ?? '', $d[$timeKey] ?? '00:00:00')
                    : null,
                $collection
            )
        );
    }

    public static function tryMapRecord(string $date, string $time): ?DateTime
    {
        try {
            $d = new DateTime($date);
            $t = DateTime::createFromFormat('H:i:s', $time) ?? DateTime::createFromFormat('H:i', $time);
            if ($d && $t) {
                return (new DateTime())
                    ->setDate((int)$d->format('Y'), (int)$d->format('m'), (int)$d->format('d'))
                    ->setTime((int)$t->format('H'), (int)$t->format('i'), (int)$t->format('s'));
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
            ->setTime($hour, $minute, $second);
    }
}
