<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapImage extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->image(
            array_values(
                array_map(
                    fn($item) => Schema::imageObject()
                        ->url($item['source_url'] ?? null)
                        ->description($item['alt_text'] ?? null)
                        ->caption($item['alt_text'] ?? null),
                    $data['_embedded']['wp:featuredmedia'] ?? []
                )
            )
        );
    }
}
