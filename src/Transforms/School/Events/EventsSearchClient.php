<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

interface EventsSearchClient
{
    public function searchEventsBySchool(string $schoolName, array $eventOrganizations): array;
}
