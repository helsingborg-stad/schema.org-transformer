<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapImage extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }
    public function map(Event $event, array $data): Event
    {
        return $event->image(
            array_map(
                fn ($path) => Schema::imageObject()
                        ->url($path)
                        ->name($data['SubTitle'] ?? null)
                        ->caption($data['SubTitle'] ?? null)
                        ->description($data['SubTitle'] ?? null),
                array_filter(
                    [($data['HasFeaturedImage'] ?? null) ? $data['FeaturedImagePath'] ?? null : null]
                )
            )
        );
    }
}
