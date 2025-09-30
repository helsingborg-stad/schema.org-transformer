<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapOrganizer extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $data['Organisation'] ?? null ? $event->organizer(
            [Schema::organization()->name($data['Organisation'] ?? null)]
        ) : $event;
    }
}
