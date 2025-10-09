<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapIsAccessibleForFree extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $prices = array_filter(
            array_map(
                fn ($price) => $price['price'] ?? null,
                $data['acf']['pricesList'] ?? []
            ),
            fn ($price) => is_numeric($price)
        );

        if (empty($prices)) {
            return $event->isAccessibleForFree(true);
        }

        return $event->isAccessibleForFree(
            !(min($prices) > 0)
        );
    }
}
