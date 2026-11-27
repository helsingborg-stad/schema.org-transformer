<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapStartDate extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $dates = array_filter(array_map(
            fn (array $show) => $show['startDate'] ?? null,
            $this->getShows($data)
        ));

        return $event->startDate($dates === [] ? null : min($dates));
    }
}
