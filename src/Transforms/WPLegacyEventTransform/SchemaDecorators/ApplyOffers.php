<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Contracts\OfferContract;
use Municipio\Schema\Schema;

class ApplyOffers implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $event = $this->applyOfferByKey($event, 'price_adult', 'Standard/Vuxen', $data);
        $event = $this->applyOfferByKey($event, 'price_children', 'Barn', $data);
        $event = $this->applyOfferByKey($event, 'price_senior', 'Pensionär', $data);
        $event = $this->applyOfferWithPriceRange($event, $data['price_range']['seated_minimum_price'] ?? null, $data['price_range']['seated_maximum_price'] ?? null, 'Sittplats', $data);
        $event = $this->applyOfferWithPriceRange($event, $data['price_range']['standing_minimum_price'] ?? null, $data['price_range']['standing_maximum_price'] ?? null, 'Ståplats', $data);

        return $event;
    }

    private function applyOfferByKey(BaseType $event, string $key, string $label, array $data): BaseType
    {
        if (empty($data[$key])) {
            return $event;
        }

        return $this->applyOffer($event, Schema::offer()
            ->price((int)$data[$key])
            ->priceCurrency('SEK')
            ->name($label), $data);
    }

    private function applyOfferWithPriceRange(BaseType $event, mixed $minValue, mixed $maxValue, string $label, array $data): BaseType
    {
        if (empty($minValue) || empty($maxValue)) {
            return $event;
        }


        return $this->applyOffer($event, Schema::offer()
            ->priceCurrency('SEK')
            ->name($label)
            ->priceSpecification(Schema::priceSpecification()
                ->minPrice((int)$minValue)
                ->maxPrice((int)$maxValue)), $data);
    }

    private function applyOffer(BaseType $event, OfferContract $offer, array $data): BaseType
    {
        $offer->url($data['booking_link'] ?? null);
        $offers   = $event->getProperty('offers') ?? [];
        $offers[] = $offer;
        return $event->setProperty('offers', $offers);
    }
}
