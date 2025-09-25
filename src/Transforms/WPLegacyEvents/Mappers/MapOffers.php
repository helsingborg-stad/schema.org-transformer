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
        $bookingLink   = $data['booking_link'] ?? null;
        $defaultOffers = empty($bookingLink) ? [] : [
            Schema::offer()
                ->url($bookingLink)
        ];
        $offers        =             array_values(
            array_filter([
                    $this->tryMakeOffer('Standard/Vuxen', $data['price_adult'] ?? null, $bookingLink),
                    $this->tryMakeOffer('Barn', $data['price_children'] ?? null, $bookingLink),
                    $this->tryMakeOffer('Student', $data['price_student'] ?? null, $bookingLink),
                    $this->tryMakeOffer('Pensionär', $data['price_senior'] ?? null, $bookingLink),
                    $this->tryMakeOfferWithPriceRange(
                        'Sittplats',
                        $data['price_range']['seated_minimum_price'] ?? null,
                        $data['price_range']['seated_maximum_price'] ?? null,
                        $bookingLink
                    ),
                    $this->tryMakeOfferWithPriceRange(
                        'Ståplats',
                        $data['price_range']['standing_minimum_price'] ?? null,
                        $data['price_range']['standing_maximum_price'] ?? null,
                        $bookingLink
                    )])
        );

        return $event->offers(
            empty($offers)
            ? $defaultOffers
            : $offers
        );
    }

    private function tryMakeOffer(string $label, $price, $bookingLink)
    {
        return empty($price) ? null : Schema::offer()
            ->price($price)
            ->priceCurrency('SEK')
            ->name($label)
            ->url($bookingLink);
    }
    private function tryMakeOfferWithPriceRange(string $label, $minPrice, $maxPrice, $bookingLink)
    {
        return (empty($minPrice) || empty($maxPrice)) ? null : Schema::offer()
            ->priceCurrency('SEK')
            ->name($label)
            ->url($bookingLink)
            ->priceSpecification(Schema::priceSpecification()
                ->minPrice($minPrice)
                ->maxPrice($maxPrice));
    }
}
