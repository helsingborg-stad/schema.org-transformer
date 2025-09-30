<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractDateMapper;

class MapEndDate extends AbstractDateMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $endDates = $this->tryMapDates(
            $data['acf']['occasions'] ?? [],
            'untilDate',
            'endTime'
        );
        return $event->endDate(
            empty($endDates) ? null : max($endDates)
        );
    }
}
