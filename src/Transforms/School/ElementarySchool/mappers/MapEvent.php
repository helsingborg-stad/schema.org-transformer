<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;

class MapEvent extends AbstractElementarySchoolDataMapper
{
    private EventsSearchClient $eventsSearchClient;

    public function __construct(EventsSearchClient $eventsSearchClient)
    {
        $this->eventsSearchClient = $eventsSearchClient;
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        $schoolName = $data['title']['rendered'] ?? '';
        return $school->event(
            $this->eventsSearchClient->searchEventsBySchoolName($schoolName)
        );
    }
}
