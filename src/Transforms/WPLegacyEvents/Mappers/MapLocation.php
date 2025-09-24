<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapLocation extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $data['location'] ?? null ? $event->location(
            Schema::place()->name($data['location']['title'] ?? null)
            ->address($data['location']['formatted_address'] ?? null)
            ->latitude($data['location']['latitude'] ?? null)
            ->longitude($data['location']['longitude'] ?? null)
        ) : $event;
    }
}
