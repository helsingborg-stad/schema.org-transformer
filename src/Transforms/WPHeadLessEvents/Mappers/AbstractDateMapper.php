<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use DateTime;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

abstract class AbstractDateMapper extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function tryMapDates(array $collection, string $dateKey, string $timeKey): array
    {
        return array_filter(
            array_map(
                fn ($d) =>
                    $this->tryParseDate($d[$dateKey] ?? '', $d[$timeKey] ?? '00:00:00'),
                $collection
            )
        );
        return $dates;
    }

    abstract public function map(Event $event, array $data): Event;

    private function tryParseDate(string $date, string $time): ?string
    {
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
