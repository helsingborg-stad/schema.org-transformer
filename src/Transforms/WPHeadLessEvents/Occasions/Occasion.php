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
        return (self::parseDate($date, 'Y-m-d') ?? self::parseDate($date, 'Ymd'))
            ?->setTime(0, 0, 0);
    }

    public static function tryMapRecords(array $collection, string $dateKey, string $timeKey): array /* of DateTime */
    {
        return array_filter(
            array_map(
                fn ($d) =>
                    $d[$dateKey] ?? null
                    ? self::tryMapRecord($d, $dateKey, $timeKey)
                    : null,
                $collection
            )
        );
    }

    public static function tryMapRecord(array $record, string $dateKey, string $timeKey): ?DateTime
    {
        return self::tryParseSeparateDateAndTime($record, $record[$dateKey] ?? '', $record[$timeKey] ?? '');
    }

    public static function tryParseSeparateDateAndTime(array $record, string $date, string $time): ?DateTime
    {
        if (empty($date)) {
            return null;
        }
        $d = self::parseDate($date, 'Y-m-d') ?? self::parseDate($date, 'Ymd');
        $t = empty($time)
            ? self::parseDate('00:00:00', 'H:i:s')
            : self::parseDate($time, 'H:i:s') ?? self::parseDate($time, 'H:i');

        if ($d && $t) {
            return (new DateTime())
                ->setDate((int)$d->format('Y'), (int)$d->format('m'), (int)$d->format('d'))
                ->setTime((int)$t->format('H'), (int)$t->format('i'), (int)$t->format('s'));
        }
        return null;
    }

    private static function parseDate(mixed /* but string */ $date, string $format): ?DateTime
    {
        try {
            $parsed = DateTime::createFromFormat($format, $date);
            if (!$parsed) {
                return null;
            }
            return $parsed;
        } catch (Exception | TypeError $e) {
            return null;
        }
    }
}
