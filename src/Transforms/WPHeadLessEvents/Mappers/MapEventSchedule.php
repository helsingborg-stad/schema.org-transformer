<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use DateTime;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;
use DateInterval;
use DateMalformedStringException;
use DatePeriod;
use Exception;
use TypeError;

class MapEventSchedule extends AbstractWPHeadlessEventMapper
{
    private $weekDayMap = [
        'https://schema.org/Monday'    => 1,
        'https://schema.org/Tuesday'   => 2,
        'https://schema.org/Wednesday' => 3,
        'https://schema.org/Thursday'  => 4,
        'https://schema.org/Friday'    => 5,
        'https://schema.org/Saturday'  => 6,
        'https://schema.org/Sunday'    => 7,
    ];

    private array $weekdayNames = [
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday',
                7 => 'sunday',

    ];
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $schedules = array_values(
            array_filter(
                array_merge(
                    ...array_map(
                        fn ($occasion) =>
                            $this->tryMapMonthlyOccasionToSchedules($occasion)
                            ?? $this->tryMapWeeklyOccasionToSchedules($occasion)
                            ?? $this->mapOccassionToSchedules($occasion),
                        $data['acf']['occasions'] ?? []
                    )
                )
            )
        );
        usort($schedules, fn ($schedule1, $schedule2) => $schedule1->getProperty('startDate') <=> $schedule2->getProperty('startDate'));
        return $event->eventSchedule($schedules);
    }

    public function mapOccassionToSchedules($occasion): ?array /* of Schema::schedule() */
    {
        return [
            Schema::schedule()
                ->startDate($this->tryParseDate($occasion['date'])?->format('Y-m-d'))
                ->endDate($this->tryParseDate($occasion['untilDate'])?->format('Y-m-d'))
                ->startTime($occasion['startTime'] ?? null)
                ->endTime($occasion['endTime'] ?? null)
                ->description($occasion['description'] ?? null)
        ];
    }

    public function tryMapWeeklyOccasionToSchedules($occasion): ?array /* of Schema::schedule() */
    {
        if ($occasion['repeat'] !== 'byWeek') {
            return null;
        }
        if (empty($occasion['date']) || empty($occasion['untilDate']) || empty($occasion['weekDays'])) {
            return null;
        }
        return array_merge(
            ...array_map(
                fn ($weekDay) => array_map(
                    fn ($date) => Schema::schedule()
                        ->startDate($date->format('Y-m-d'))
                        ->endDate($date->format('Y-m-d'))
                        ->startTime($occasion['startTime'] ?? null)
                        ->endTime($occasion['endTime'] ?? null)
                        ->description($occasion['description'] ?? null),
                    $this->expandWeeklyDates(
                        $this->tryParseDate($occasion['date'] ?? ''),
                        $this->tryParseDate($occasion['untilDate'] ?? ''),
                        $this->weekDayMap[$weekDay] ?? null,
                        (int)($occasion['weeksInterval'] ?? 1)
                    )
                ),
                $occasion['weekDays'] ?? []
            )
        );
    }

    public function tryMapMonthlyOccasionToSchedules($occasion): ?array /* of Schema::schedule() */
    {
        // TODO: understand and implement monthly recurrence
        return null;
    }

    public function expandWeeklyDates(
        DateTime $start, // assumed date only
        DateTime $end, // assumed date only
        int $dayOfWeek,
        int $weekInterval = 1
    ): array {
        // adjust start to next occurence of weekday
        while ((int)$start->format('N') !== $dayOfWeek) {
            $start->modify('next ' . $this->weekdayNames[$dayOfWeek]);
        }
        $data     = [];
        $interval = new DateInterval('P' . $weekInterval . 'W');
        $period   = new DatePeriod($start, $interval, $end, DatePeriod::INCLUDE_END_DATE);
        foreach ($period as $date) {
            $data[] = (clone $date);
        }
        return $data;
    }

    private function tryParseDate($date): ?DateTime
    {
        try {
            return new DateTime($date);
        } catch (DateMalformedStringException | Exception | TypeError $e) {
            return null;
        }
    }
}
