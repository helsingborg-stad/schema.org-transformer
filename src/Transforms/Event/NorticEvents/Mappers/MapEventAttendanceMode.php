<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapEventAttendanceMode extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->eventAttendanceMode('https://schema.org/OfflineEventAttendanceMode');
    }
}
