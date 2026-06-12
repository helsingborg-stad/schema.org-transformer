<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Util\DateUtils;

class MapStartDate extends AbstractAxiellEventsDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
            ->startDate(DateUtils::toLocalDate($data['startDate'] ?? null));
    }
}
