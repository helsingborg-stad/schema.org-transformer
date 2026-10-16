<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
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
            array_filter(
                array_values(
                    array_map(
                        fn($item) => $item['source_url'] ?? null
                        ? Schema::imageObject()
                            ->url($item['source_url'] ?? null)
                            ->description($item['alt_text'] ?? null)
                            ->caption($item['alt_text'] ?? null)
                        : null,
                        $this->getMedia($data)
                    )
                )
            )
        );
    }

    private function getMedia(array $data): array
    {
        $embedded = $data['_embedded']['wp:featuredmedia'] ?? [];

        if (!empty($embedded)) {
            return $embedded;
        }

        $featured = $data['featured_media'] ?? null;

        return is_array($featured) && !empty($featured['source_url']) ? [$featured] : [];
    }
}
