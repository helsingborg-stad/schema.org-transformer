<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapIsAccessibleForFree extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $maxPrice = array_map(
            fn($key) => $data[$key] ?? 0,
            ['price_adult',
            'price_children',
            'children_age',
            'price_student',
            'price_senior']
        );

        return $event->isAccessibleForFree($maxPrice > 0);
    }
}
