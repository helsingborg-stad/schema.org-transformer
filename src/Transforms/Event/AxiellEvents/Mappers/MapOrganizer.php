<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapOrganizer extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $location = $data['location']['value'] ?? null;
        return empty($location) ? $event : $event
            ->organizer([
                Schema::organization()
                    ->name($location)
            ]);
    }
}
