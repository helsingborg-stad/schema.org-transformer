<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;

class MapXCreatedBy extends AbstractAxiellEventsDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->setProperty('x-created-by', 'municipio://schema.org-transformer/axiell-events');
    }
}
