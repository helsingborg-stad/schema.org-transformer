<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapOrganizer extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $organizerName = $data['organizerName'] ?? null;

        return $event->organizer(
            $organizerName === null ? [] : [Schema::organization()->name($organizerName)]
        );
    }
}
