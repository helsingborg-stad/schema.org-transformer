<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapImage extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $imageUrl = $data['imageUrl'] ?? null;

        return $event->image(
            empty($imageUrl)
                ? []
                : [
                    Schema::imageObject()->url($imageUrl)
                    ->description(null)
                    ->caption(null)
                ]
        );
    }
}
