<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapImage extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event
            ->image(
                array_filter(
                    array_values(
                        array_map(
                            fn($img) => empty($img['imageUrl'])
                                ? null
                                : Schema::imageObject()
                                    ->url($img['imageUrl'] ?? null)
                                    ->caption($img['imageCaption'] ?? null)
                                    ->description($img['imageCaption'] ?? null),
                            $data['images'] ?? []
                        )
                    )
                )
            );
    }
}
