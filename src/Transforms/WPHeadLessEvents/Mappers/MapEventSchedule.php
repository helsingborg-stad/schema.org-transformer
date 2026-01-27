<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\Occasion;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\WeeklyOccasion;

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
                            $this->tryMapWeeklyOccasionToSchedules($occasion)
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
        $startDateTime = (!empty($occasion['date']) && !empty($occasion['startTime'])) ? date('Y-m-d H:i:s', strtotime($occasion['date'] . ' ' . $occasion['startTime'])) : null;
        $endDateTime   = (!empty($occasion['untilDate']) && !empty($occasion['endTime'])) ? date('Y-m-d H:i:s', strtotime($occasion['untilDate'] . ' ' . $occasion['endTime'])) : null;

        return [
            Schema::schedule()
                ->startDate($startDateTime)
                ->endDate($endDateTime)
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
                    function ($date) use ($occasion) {
                        $startDateTime = (!empty($occasion['startTime'])) ? date('Y-m-d H:i:s', strtotime($date->format('Y-m-d') . ' ' . $occasion['startTime'])) : null;
                        $endDateTime   = (!empty($occasion['endTime'])) ? date('Y-m-d H:i:s', strtotime($date->format('Y-m-d') . ' ' . $occasion['endTime'])) : null;
                        return Schema::schedule()
                            ->startDate($startDateTime)
                            ->endDate($endDateTime)
                            ->description($occasion['description'] ?? null);
                    },
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
}
