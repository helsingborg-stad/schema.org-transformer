<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapEndDate extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        $endDates = array_filter(array_map(
            fn ($d) => $d['end_date'] ?? null,
            $data['all_occasions'] ?? []
        ));
        return empty($endDates) ? $event : $event->endDate(
            max($endDates)
        );
    }
}
