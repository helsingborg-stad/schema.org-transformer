<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

class MapEventSchedule extends AbstractTixDataMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }
    public function map(Event $event, array $data): Event
    {
        return $event->eventSchedule(
            array_values(
                array_map(
                    fn($d) => Schema::schedule()
                            ->startDate($d['StartDate'] ?? null)
                            ->endDate($d['EndDate'] ?? null)
                            ->identifier($this->formatId((string)$data['EventGroupId'] ?? '') . '_' . ($d['EventId'] ?? '')),
                    $this->getValidDatesFromSource($data)
                )
            )
        );
    }
}
