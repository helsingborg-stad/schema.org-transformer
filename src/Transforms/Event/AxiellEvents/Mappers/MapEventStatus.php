<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapEventStatus extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->eventStatus(Schema::eventStatusType()::EventScheduled);
    }
}
