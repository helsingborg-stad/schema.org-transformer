<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapOffers extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->offers(
            array_values(
                array_filter(
                    array_map(
                        fn ($pl) => is_numeric($pl['price'] ?? null)
                            ? Schema::offer()
                                ->name($pl['priceLabel'] ?? null)
                                ->price((int)$pl['price'] ?? null)
                                ->priceCurrency('SEK')
                                ->businessFunction('http://purl.org/goodrelations/v1#Sell')
                            : null,
                        $data['acf']['pricesList'] ?? []
                    )
                )
            )
        );
    }
}
