<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapOrganizer extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->organizer(
            array_map(function ($organizer) {
                return Schema::organization()
                ->name($organizer['title']['rendered'] ?? null)
                ->url($organizer['website'] ?? null)
                ->email($organizer['email'] ?? null)
                ->telephone($organizer['phone'] ?? null);
            },
            $data['_embedded']['organizers'] ?? [])
        );
    }
}
