<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;

class MapStartDate extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $startDates = OccasionHelper::tryMapDatesAndTimes(
            $data['acf']['occasions'] ?? [],
            'date',
            'startTime'
        );
        return $event->startDate(
            empty($startDates) ? null : min($startDates)
        );
    }
}
