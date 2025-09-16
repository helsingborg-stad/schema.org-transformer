<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

interface EventsSearchClient
{
    public function searchEventsBySchoolName(string $schoolName): array;
}
