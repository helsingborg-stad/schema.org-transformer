<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;

class MapEvent extends AbstractPreSchoolDataMapper
{
    private EventsSearchClient $eventsSearchClient;

    public function __construct(EventsSearchClient $eventsSearchClient)
    {
        $this->eventsSearchClient = $eventsSearchClient;
    }

    public function map(Preschool $school, array $data): Preschool
    {
        $schoolName = $data['title']['rendered'] ?? '';
        return $school->event(
            $this->eventsSearchClient->searchEventsBySchoolName($schoolName)
        );
    }
}
