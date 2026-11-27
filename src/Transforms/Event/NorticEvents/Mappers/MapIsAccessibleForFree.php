<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

class MapIsAccessibleForFree extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $prices = [];
        foreach ($this->getShows($data) as $show) {
            foreach (['minPrice', 'maxPrice'] as $priceKey) {
                if (isset($show[$priceKey]) && is_numeric($show[$priceKey])) {
                    $prices[] = (float) $show[$priceKey];
                }
            }
        }

        return $event->isAccessibleForFree($prices !== [] && min($prices) <= 0);
    }
}
