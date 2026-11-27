<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapUrl extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->url($data['link'] ?? null);
    }
}
