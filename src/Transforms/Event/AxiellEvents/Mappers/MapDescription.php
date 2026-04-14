<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;

class MapDescription extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event
            ->description(
                array_values(
                    array_filter(
                        [$data['description'] ?? null]
                    )
                )
            );
    }
}
