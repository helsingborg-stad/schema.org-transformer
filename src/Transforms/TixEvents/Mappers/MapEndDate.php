<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;

class MapEndDate extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }
    public function map(Event $event, array $data): Event
    {
        return $event
                ->endDate(
                    max(
                        array_filter(
                            array_map(
                                fn ($d) => $d['EndDate'] ?? null,
                                [...$this->getValidDatesFromSource($data), null]
                            ),
                            fn ($endDate) => !is_null($endDate)
                        )
                    ) ?? null
                );
    }
}
