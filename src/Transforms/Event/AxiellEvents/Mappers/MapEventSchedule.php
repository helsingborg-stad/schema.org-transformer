<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Util\DateUtils;

class MapEventSchedule extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->eventSchedule(
            array_values(
                array_filter([
                    empty($data['startDate'])
                        ? null
                        : Schema::schedule()
                            ->startDate(DateUtils::toLocalDate($data['startDate'] ?? null))
                            ->endDate(DateUtils::toLocalDate($data['endDate'] ?? null))
                            ->description(null)
                            ->url(null)
                ])
            )
        );
    }
}
