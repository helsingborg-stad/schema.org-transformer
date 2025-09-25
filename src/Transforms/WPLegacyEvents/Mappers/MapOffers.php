<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapOffers extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->offers(
            array_filter(
                [$this->tryMakeOffer('Standard/Vuxen', $data['price_adult'] ?? null),
                $this->tryMakeOffer('Barn', $data['price_children'] ?? null),
                $this->tryMakeOffer('Student', $data['price_student'] ?? null),
                $this->tryMakeOffer('Pensionär', $data['price_senior'] ?? null, 'Pensionär'),
                $this->tryMakeOfferWithPriceRange(
                    'Sittplats',
                    $data['price_range']['seated_minimum_price'] ?? null,
                    $data['price_range']['seated_maximum_price'] ?? null
                ),
                    $this->tryMakeOfferWithPriceRange(
                        'Ståplats',
                        $data['price_range']['standing_minimum_price'] ?? null,
                        $data['price_range']['standing_maximum_price'] ?? null
                    )]
            )
        );
    }

    private function tryMakeOffer(string $label, $price)
    {
        return empty($price) ? null : Schema::offer()
            ->price($price)
            ->priceCurrency('SEK')
            ->name($label);
    }
    private function tryMakeOfferWithPriceRange(string $label, $minPrice, $maxPrice)
    {
        return (empty($minPrice) || empty($maxPrice)) ? null : Schema::offer()
            ->priceCurrency('SEK')
            ->name($label)
            ->priceSpecification(Schema::priceSpecification()
                ->minPrice($minPrice)
                ->maxPrice($maxPrice));
    }
}
