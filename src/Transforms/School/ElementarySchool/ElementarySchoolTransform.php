<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;
use SchemaTransformer\Transforms\School\Events\NullEventsSearchClient;
use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapAfterSchoolCare;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapAreaServed;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapContactPoint;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapDescription;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapEmployee;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapEvent;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapHasOfferCatalog;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapImage;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapKeywords;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapLocation;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapName;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapNumberOfStudents;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapVideo;
use SchemaTransformer\Transforms\School\ElementarySchool\Mappers\MapXCreatedBy;

class ElementarySchoolTransform extends TransformBase implements AbstractDataTransform
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'custom_excerpt'     => '',
        'about_us'           => 'Om oss',
        'how_we_work'        => 'Så arbetar vi',
        'our_leisure_center' => 'Vårt fritidshem'
    ];

    private array $taxonomiesExcludedFromKeywords = [
        'area'  => true,
        'grade' => true,
    ];

    private EventsSearchClient $eventsSearchClient;

    /**
     * ElementarySchoolTransform constructor.
     */
    public function __construct(string $idprefix = '', private ?TypesenseClient $typesenseClient = null)
    {
        parent::__construct($idprefix);
        $this->eventsSearchClient = $typesenseClient
            ? new EventsSearchClientOnTypesense($typesenseClient)
            : new NullEventsSearchClient();
    }

    public function withEventSearchClient(EventsSearchClient $client): ElementarySchoolTransform
    {
        $this->eventsSearchClient = $client;
        return $this;
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapAfterSchoolCare(),
            new MapAreaServed(),new MapContactPoint(),
            new MapDescription(),
            new MapEmployee(),
            new MapEvent($this->eventsSearchClient),
            new MapHasOfferCatalog(),
            new MapIdentifier($this),
            new MapImage(),
            new MapKeywords(),
            new MapLocation(),
            new MapName(),
            new MapNumberOfStudents(),
            new MapPotentialAction(),
            new MapVideo(),
            new MapXCreatedBy()

        ];
        return array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($school, $mapper) use ($item) {
                    return $mapper->map($school, $item);
                },
                Schema::elementarySchool()
            )->toArray();
        }, $data);
    }
}
