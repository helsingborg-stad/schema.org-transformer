<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapPhysicalAccessibilityFeatures extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->physicalAccessibilityFeatures([]);
    }
}
