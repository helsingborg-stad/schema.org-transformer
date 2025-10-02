<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\Occasion;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\WeeklyOccasion;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\MonthlyOccasion;

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
                ->startDate(Occasion::tryParseDate($occasion['date'])?->format('Y-m-d'))
                ->endDate(Occasion::tryParseDate($occasion['untilDate'])?->format('Y-m-d'))
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
                    WeeklyOccasion::getDatesInPeriod(
                        Occasion::tryParseDate($occasion['date'] ?? ''),
                        Occasion::tryParseDate($occasion['untilDate'] ?? ''),
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
        if ($occasion['repeat'] !== 'byMonth') {
            return null;
        }
        if (empty($occasion['date']) || empty($occasion['untilDate']) || !is_numeric($occasion['monthDayNumber'])) {
            return null;
        }

        return array_map(
            fn ($date) => Schema::schedule()
                ->startDate($date->format('Y-m-d'))
                ->endDate($date->format('Y-m-d'))
                ->startTime($occasion['startTime'] ?? null)
                ->endTime($occasion['endTime'] ?? null)
                ->description($occasion['description'] ?? null),
            MonthlyOccasion::getDatesInPeriod(
                Occasion::tryParseDate($occasion['date'] ?? ''),
                Occasion::tryParseDate($occasion['untilDate'] ?? ''),
                (int)($occasion['monthDayNumber'] ?? 1),
                (int)($occasion['monthsInterval'] ?? 1)
            )
        );
    }
}
