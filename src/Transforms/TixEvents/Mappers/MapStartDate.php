<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;

class MapStartDate extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $startDates = array_map(
            fn ($d) => $d['StartDate'] ?? null,
            $this->getValidDatesFromSource($data)
        );
        return empty($startDates) ? $event : $event->startDate(
            min($startDates)
        );
    }
}
