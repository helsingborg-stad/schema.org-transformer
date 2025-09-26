<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;

class MapIsAccessibleForFree extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }
    public function map(Event $event, array $data): Event
    {
        return $event->isAccessibleForFree(
            array_reduce(
                $this->getValidDatesFromSource($data),
                fn ($carry, $d) => $carry || ($d['IsFreeEvent'] ?? false),
                false
            ) || false
        );
    }
}
