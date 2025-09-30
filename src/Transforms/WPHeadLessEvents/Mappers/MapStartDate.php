<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractDateMapper;

class MapStartDate extends AbstractDateMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $startDates = $this->tryMapDates(
            $data['acf']['occasions'] ?? [],
            'date',
            'startTime'
        );
        return $event->startDate(
            empty($startDates) ? null : min($startDates)
        );
    }
}
