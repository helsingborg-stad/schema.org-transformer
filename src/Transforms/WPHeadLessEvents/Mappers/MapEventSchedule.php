<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use DateTime;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapEventSchedule extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->eventSchedule(
            array_values(
                array_filter(
                    array_map(
                        fn ($occasion) =>
                            Schema::schedule()
                                ->startDate(OccasionHelper::tryMapDate($occasion['date'] ?? ''))
                                ->endDate(OccasionHelper::tryMapDate($occasion['untilDate'] ?? ''))
                                ->startTime($occasion['startTime'] ?? null)
                                ->endTime($occasion['endTime'] ?? null)
                                ->byDay($occasion['weekDays'] ?? null),
                        $data['acf']['occasions'] ?? []
                    )
                )
            )
        );
    }

    private function tryParseDate($date): ?DateTime
    {
        try {
            return new DateTime($date);
        } catch (\Exception) {
            return null;
        }
    }
}
