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
        return $event->offers(
            array_merge(
                $this->tryCreatePurchaseOffers($data) ?? [],
                // $this->tryCreateProductOffers($data) ?? []
            ),
        );
    }

    private function tryCreatePurchaseOffers($data): ?array
    {
        // Iterate eagerly to find the first date with purchase and prices
        foreach ($this->getValidDatesFromSource($data) as $date) {
            $offers = $this->tryCreatePurchaseOffersFromDateLikeObject($date);
            if ($offers !== null) {
                return $offers;
            }
        }
        // Test if event group itself has purchase and prices
        return $this->tryCreatePurchaseOffersFromDateLikeObject($data); // No valid offers found
    }

    private function tryCreatePurchaseOffersFromDateLikeObject($dateLike): ?array
    {
        // Iterate eagerly to find the first date with products and prices
        $purchaseUrls = array_filter(
            $dateLike['PurchaseUrls'] ?? [],
            fn ($d) => $d['Culture'] === 'sv-SE'
        );
        if (empty($purchaseUrls)) {
            return null;
        }
            $prices = $dateLike['Prices'] ?? null;
        if (empty($prices)) {
            return null;
        }

        foreach ($purchaseUrls as $purchaseUrl) {
            return [
                Schema::offer()
                    ->availabilityStarts($dateLike['OnlineSaleStart'] ?? null)
                    ->availabilityEnds($dateLike['OnlineSaleEnd'] ?? null)
                    ->url($purchaseUrl['Link'] ?? null)
                    ->mainEntityOfPage($purchaseUrl['Link'] ?? null)
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell')

                ->priceSpecification(
                    array_values(
                        array_map(
                            fn ($p) => Schema::priceSpecification()
                                    ->name($p['TicketType'] ?? null)
                                    ->description($p['TicketType'] ?? null)
                                    ->priceCurrency('SEK')
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

    private function tryCreateProductOffers($data): ?array
    {
        // Iterate eagerly to find the first date with products
        foreach ($this->getValidDatesFromSource($data) as $date) {
            $offers = $this->tryCreateProductOffersFromDateLikeObject($date);
            if ($offers !== null) {
                return $offers;
            }
        }
        // Test if event group itself has products
        return $this->tryCreateProductOffersFromDateLikeObject($data); // No valid offers found
    }

    private function tryCreateProductOffersFromDateLikeObject($data): ?array
    {
        $productPurchaseUrls = array_filter(
            $data['ProductPurchaseUrls'] ?? [],
            fn ($d) => $d['Culture'] === 'sv-SE'
        );
        if (empty($productPurchaseUrls)) {
            return null;
        }
        $products = $data['Products'] ?? null;
        if (empty($products)) {
            return null;
        }

        $link = $productPurchaseUrls[0]['Link'] ?? null;

        foreach ($products as $product) {
            return [
                Schema::offer()
                    ->url($link)
                    ->mainEntityOfPage($link)
                    ->name($product['Name'] ?? null)
                    ->description($product['Description'] ?? null)
                    ->price($product['Price'] ?? null)
                    ->priceCurrency('SEK')
                    ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                    ->image(
                        ($product['ProductImagePath'] ?? null) ?
                            Schema::imageObject()
                                ->name($product['Name'] ?? null)
                                ->description($product['Name'] ?? null)
                                ->caption($product['Name'] ?? null)
                                ->url($product['ProductImagePath'] ?? null)
                         : null
                    )
                ];
        }
        return null;
    }
}
