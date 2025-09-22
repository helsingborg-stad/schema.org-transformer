<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapLocation extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        foreach ($this->getValidDatesFromSource($data) as $date) {
            return $event->location(
                Schema::place()
                ->name($date['Venue'] ?? null)
                ->description($date['Hall'] ?? null)
            );
        }
        return $event;
    }
}
