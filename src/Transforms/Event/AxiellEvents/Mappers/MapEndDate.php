<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Util\DateUtils;

class MapEndDate extends AbstractAxiellEventsDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
            ->endDate(DateUtils::toLocalDate($data['endDate'] ?? null));
    }
}
