<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;

interface AxiellEventsDataMapperInterface
{
    public function map(Event $event, array $data): Event;
}
