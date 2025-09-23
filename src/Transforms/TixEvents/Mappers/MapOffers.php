<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapOffers extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        // Iterate eagerly to find the first date with products and prices
        foreach ($this->getValidDatesFromSource($data) as $date) {
            $offers = $this->tryCreateOffers($date);
            if ($offers !== null) {
                return $event->offers($offers);
            }
        }
        return $event->offers($this->tryCreateOffers($data) ?? []); // No valid offers found
    }

    private function tryCreateOffers($date): ?array
    {
        // Iterate eagerly to find the first date with products and prices
        $products = array_filter(
            $date['PurchaseUrls'] ?? [],
            fn ($d) => $d['Culture'] === 'sv-SE'
        );
        if (empty($products)) {
            return null;
        }
            $prices = $date['Prices'] ?? null;
        if (empty($prices)) {
            return null;
        }

        foreach ($products as $product) {
            return [
                Schema::offer()
                    ->availabilityStarts($date['OnlineSaleStart'] ?? null)
                    ->availabilityEnds($date['OnlineSaleEnd'] ?? null)
                    ->url($product['Link'] ?? null)
                    ->mainEntityOfPage($product['Link'] ?? null)
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell')

                ->priceSpecification(
                    array_values(
                        array_map(
                            fn ($p) => Schema::priceSpecification()
                                    ->name($p['TicketType'] ?? null)
                                    ->description($p['TicketType'] ?? null)
                                    ->price(
                                        array_filter(
                                            array_values(
                                                array_map(
                                                    fn ($pp) => $pp['Price'] ?? null,
                                                    $p['Prices'] ?? []
                                                )
                                            ),
                                            fn ($val) => is_numeric($val)
                                        )
                                    ),
                            $prices
                        )
                    )
                )
            ];
        }
        return null;
    }
}
