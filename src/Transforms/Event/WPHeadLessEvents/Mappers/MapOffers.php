<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapOffers extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        // booking link will be first good url from an occasion
        $candidateUrl = array_values(array_filter(
            array_map(
                fn ($occasion) => $occasion['url'] ?? null,
                $data['acf']['occasions'] ?? []
            )
        ))[0] ?? null;

        $offers =
            array_values(
                array_filter(
                    array_map(
                        fn ($pl) => is_numeric($pl['price'] ?? null)
                            ? Schema::offer()
                                ->name($pl['priceLabel'] ?? null)
                                ->url($candidateUrl)
                                ->priceSpecification(
                                    Schema::priceSpecification()
                                        ->name($pl['priceLabel'] ?? null)
                                        ->price((int)$pl['price'] ?? null)
                                        ->priceCurrency('SEK')
                                )
                            : null,
                        $data['acf']['pricesList'] ?? []
                    )
                )
            );

        return $event->offers(
            empty($offers)
            ? (empty($candidateUrl) ? [] : [Schema::offer()->url($candidateUrl)])
            : $offers
        );
    }
}
