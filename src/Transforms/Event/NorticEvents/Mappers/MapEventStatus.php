<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapEventStatus extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->eventStatus(Schema::eventStatusType()::EventScheduled);
    }
}
