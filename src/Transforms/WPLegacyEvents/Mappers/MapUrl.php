<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapUrl extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->url($data['event_link'] ?? null);
    }
}
