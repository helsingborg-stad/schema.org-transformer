<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapEventSchedule extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->eventSchedule([
            Schema::schedule()
                ->startDate($data['startDate'] ?? null)
                ->endDate($data['endDate'] ?? null)
                ->description(null)
                ->url(null)
        ]);
    }
}
