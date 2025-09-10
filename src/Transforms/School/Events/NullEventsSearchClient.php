<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

class NullEventsSearchClient implements EventsSearchClient
{
    public function searchEventsBySchoolName(string $schoolName): array
    {
        return [];
    }
}
