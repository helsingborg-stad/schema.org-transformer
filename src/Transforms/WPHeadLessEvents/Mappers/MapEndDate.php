<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Occasions\Occasion;

class MapEndDate extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $endDates = Occasion::tryMapRecords(
            $data['acf']['occasions'] ?? [],
            'untilDate',
            'endTime'
        );
        return $event->endDate(
            empty($endDates) ? null : max($endDates)
        );
    }
}
