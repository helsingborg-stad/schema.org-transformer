<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapStartDate extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $startDates = array_filter(array_map(
            fn ($d) => $d['start_date'] ?? null,
            $data['all_occasions'] ?? []
        ));
        return empty($startDates) ? $event : $event->startDate(
            min($startDates)
        );
    }
}
