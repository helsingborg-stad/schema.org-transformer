<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapLocation extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $location = $data['location']['value'] ?? null;
        return empty($location) ? $event : $event->location([
                Schema::place()
                    ->name($location)
                    ->description($data['room']['value'] ?? null)
            ]);
    }
}
