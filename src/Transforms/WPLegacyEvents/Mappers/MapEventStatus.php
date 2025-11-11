<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapEventStatus extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->eventStatus(
            match (strtolower($data['all_occasions'][0]['status'] ?? '')) {
                'rescheduled' => Schema::eventStatusType()::EventRescheduled,
                'cancelled' => Schema::eventStatusType()::EventCancelled,
                default => Schema::eventStatusType()::EventScheduled,
            }
        );
    }
}
