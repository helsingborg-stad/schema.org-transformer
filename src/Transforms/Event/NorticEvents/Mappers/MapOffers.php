<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapOffers extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $offers = [];
        foreach ($this->getShows($data) as $show) {
            $prices = array_values(array_filter(
                [$show['minPrice'] ?? null, $show['maxPrice'] ?? null],
                'is_numeric'
            ));

            if ($prices === []) {
                continue;
            }

            $offers[] = Schema::offer()
                ->url($show['link'] ?? null)
                ->priceSpecification(
                    Schema::priceSpecification()
                        ->priceCurrency('SEK')
                        ->minPrice(min($prices))
                        ->maxPrice(max($prices))
                );
        }

        return $event->offers($offers);
    }
}
