<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapDescription extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->description(
            array_values(array_filter(
                [$data['description'] ?? null]
            ))
        );
    }
}
