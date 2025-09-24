<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapTypicalAgeRange extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->typicalAgeRange(
            $this->tryGetAgeRangeString(
                $data['age_group_from'] ?? null,
                $data['age_group_to'] ?? null
            )
        );
    }

    private function tryGetAgeRangeString($from, $to): string|null
    {
        if ($from && $to) {
            return $from . '-' . $to;
        }
        if ($from) {
            return $from . '+';
        }
        if ($to) {
            return '0-' . $to;
        }
        return null;
    }
}
