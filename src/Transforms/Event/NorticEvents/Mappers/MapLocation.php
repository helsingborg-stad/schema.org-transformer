<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapLocation extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $show = $this->getShows($data)[0] ?? [];
        if (empty($show['arenaName'])) {
            return $event->location([]);
        }

        return $event->location([
            Schema::place()
                ->name($show['arenaName'])
                ->address($show['arenaAddress'] ?? null)
                ->latitude($show['arenaLatitude'] ?? null)
                ->longitude($show['arenaLongitude'] ?? null),
        ]);
    }
}
