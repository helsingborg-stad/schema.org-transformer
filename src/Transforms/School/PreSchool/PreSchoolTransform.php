<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;
use SchemaTransformer\Transforms\School\Events\NullEventsSearchClient;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapAreaServed;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapContactPoint;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapDescription;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapEmployee;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapEvent;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapImage;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapKeywords;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapLocation;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapName;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapNumberOfChildren;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapNumberOfGroups;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapOpeningHoursSpecification;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapVideo;
use SchemaTransformer\Transforms\School\PreSchool\Mappers\MapXCreatedBy;

class PreSchoolTransform extends TransformBase implements AbstractDataTransform
{
    private EventsSearchClient $eventsSearchClient;

    /**
     * PreSchoolTransform constructor.
     */
    public function __construct(string $idprefix = '', private ?TypesenseClient $typesenseClient = null)
    {
        parent::__construct($idprefix);
        $this->eventsSearchClient = $typesenseClient
            ? new EventsSearchClientOnTypesense($typesenseClient)
            : new NullEventsSearchClient();
    }

    public function withEventSearchClient(EventsSearchClient $client): PreSchoolTransform
    {
        $this->eventsSearchClient = $client;
        return $this;
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapAreaServed(),
            new MapContactPoint(),
            new MapDescription(),
            new MapEmployee(),
            new MapEvent($this->eventsSearchClient),
            new MapIdentifier($this),
            new MapImage(),
            new MapKeywords(),
            new MapLocation(),
            new MapName(),
            new MapNumberOfChildren(),
            new MapNumberOfGroups(),
            new MapOpeningHoursSpecification(),
            new MapPotentialAction(),
            new MapVideo(),
            new MapXCreatedBy()
        ];

        $result = array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($school, $mapper) use ($item) {
                    return $mapper->map($school, $item);
                },
                Schema::preschool()
            )->toArray();
        }, $data);
        return $result;
    }
}
