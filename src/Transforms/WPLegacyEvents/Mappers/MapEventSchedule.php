<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapEventSchedule extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->eventSchedule(
            array_values(
                array_map(
                    fn($d) => Schema::schedule()
                        ->startDate($d['start_date'] ?? null)
                        ->endDate($d['end_date'] ?? null)
                        ->description(
                            $d['content_mode'] === 'custom' ? ($d['content'] ?? null) : null
                        )
                        ->url($d['booking_link'] ?? null),
                    $data['all_occasions'] ?? [ ]
                )
            )
        );
    }
}
