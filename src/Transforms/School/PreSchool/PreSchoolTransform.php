<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;
use SchemaTransformer\Transforms\School\Events\NullEventsSearchClient;
use Municipio\Schema\PreSchool;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;
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

class PreSchoolTransform extends TransformBase implements AbstractDataTransform
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'custom_excerpt' => '',
        'visit_us'       => 'Besök oss',
        'about_us'       => 'Om oss',
        'how_we_work'    => 'Så arbetar vi',
        'orientation'    => 'Introduktion',
    ];

    private array $taxonomiesExcludedFromKeywords = [
        'area' => true,
    ];

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
            new MapVideo()
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

        $transformations = [
                'transformBase',
                'transformDescription',
                'transformKeywords',
                'transformPlace',
                'transformEvents',
                'transformActions',
                'transformAreaServed',
                'transformImages',
                'transformEmployees',
                'transformContactPoint',
                'transformNumberOfChildren',
                'transformOpeningHours',
                'transformNumberOfGroups',
                'transformVideo',
                ];

                $result = array_map(function ($item) use ($transformations) {
                    return array_reduce(
                        $transformations,
                        function ($school, $method) use ($item) {
                            return $this->$method($school, $item);
                        },
                        Schema::preSchool()
                    )->toArray();
                }, $data);
        return $result;
    }

    public function transformBase($school, $data): PreSchool
    {
        return $school
            ->identifier($data['id'] ?? null ? (string)$data['id'] : null)
            ->name($data['title']['rendered'] ?? null);
    }

    public function transformVideo($school, $data): PreSchool
    {
        return $school->video(
            array_values(array_map(
                fn($url) => Schema::videoObject()
                    ->url($url),
                array_filter([$data['acf']['video'] ?? null])
            ))
        );
    }

    public function transformActions($school, $data): PreSchool
    {
        $description = $data['acf']['cta_application']['description'] ?? null;

        return $school->potentialAction(
            array_values(
                array_filter(
                    array_map(
                        fn ($t, $k) =>
                                is_array($t) && is_string($t['title'] ?? null) && !empty($t['title'] ?? null)
                                ? Schema::action()->name($k)->title($t['title'])->description($description)->url($t['url'] ?? null)
                                : null,
                        ($data['acf']['cta_application'] ?? []),
                        array_keys($data['acf']['cta_application'] ?? [])
                    )
                )
            )
        );
    }

    public function transformKeywords($school, $data): PreSchool
    {
        return $school->keywords(
            array_values(
                array_filter(
                    array_map(
                        fn ($t) =>
                                !empty($t) && is_string($t['name'] ?? null) && !empty($t['name'] ?? null) && !($this->taxonomiesExcludedFromKeywords[$t['taxonomy'] ?? ''] ?? false)
                                ? Schema::definedTerm()
                                    ->name($t['name'])
                                    ->description($t['name'])
                                    ->inDefinedTermSet($t['taxonomy'] ?? null)
                                : null,
                        ($data['_embedded']['acf:term'] ?? [])
                    )
                )
            )
        );
    }

    public function transformDescription($school, $data): PreSchool
    {
        $descriptions = [
            $this->tryCreateTextObject('custom_excerpt', $data['acf']['custom_excerpt'] ?? null),
            $this->tryCreateTextObject('visit_us', $data['acf']['visit_us'] ?? null),
        ];

        foreach ($data['acf']['information'] ?? [] as $key => $text) {
            $descriptions[] =
            (
                is_string($text) ? $this->tryCreateTextObject($key, $text) : null
                ) ?? (
                is_array($text) && is_array($text[0]) ?
                $this->tryCreateTextObject($text[0]['heading'], $text[0]['content']) : null
                );
        }
        foreach ($data['pages_embedded'] ?? [] as $page) {
            array_push($descriptions, $this->tryCreateTextObject($page['post_title'] ?? null, $page['post_content'] ?? null));
        }

        return $school
            ->description(array_values(array_filter($descriptions)));
    }

    public function transformPlace($school, $data): PreSchool
    {
        return $school->location(array_filter(
            array_values(
                array_map(
                    fn ($address) =>
                        $address['address'] ?? null
                        ? Schema::place()
                            ->name($address['address']['name'] ?? null)
                            ->address($address['address']['address'] ?? null)
                            ->latitude($address['address']['lat'] ?? null)
                            ->longitude($address['address']['lng'] ?? null)
                            ->description($address['address']['description'] ?? null)
                        : null,
                    ($data['acf']['visiting_address'] ?? [])
                )
            )
        ));
    }

    public function transformEvents(PreSchool $school, $data): PreSchool
    {
        return $school->event(
            $this->eventsSearchClient->searchEventsBySchoolName($school->getProperty('name') ?? '')
        );
    }

    public function transformAreaServed($school, $data): PreSchool
    {
        return $school->areaServed(array_values(array_filter(
            array_map(
                fn ($t) =>
                        !empty($t) && is_string($t['name'] ?? null) && !empty($t['name'] ?? null) && ($t['taxonomy'] ?? null) === 'area'
                        ? $t['name']
                        : null,
                ($data['_embedded']['acf:term'] ?? [])
            )
        )));
    }

    public function transformImages($school, $data): PreSchool
    {
        return $school->image(
            array_map(
                fn($image) => Schema::imageObject()
                    ->name($image['title'] ?? null)
                    ->caption($image['caption'] ?? null)
                    ->description($image['alt'] ?? null)
                    ->url($image['url'] ?? null),
                $data['images'] ?? []
            )
        );
    }

    public function transformEmployees($school, $data): PreSchool
    {
        return $school->employee(
            array_map(
                fn($person) => Schema::person()
                    ->name($person['name'] ?? null)
                    ->jobTitle($person['job_title'] ?? null)
                    ->email($person['email'] ?? null)
                    ->telephone($person['telephone'] ?? null)
                    ->image(
                        isset($person['image']) && is_array($person['image']) ?
                        Schema::imageObject()
                            ->name($person['image']['name'] ?? null)
                            ->caption($person['image']['caption'] ?? null)
                            ->description($person['image']['alt'] ?? null)
                            ->url($person['image']['url'] ?? null)
                        : null
                    ),
                $data['employee'] ?? []
            )
        );
    }

    public function transformContactPoint($school, $data): PreSchool
    {
        return $school->contactPoint(
            array_values(
                array_filter(
                    [
                        $data['acf']["link_facebook"] ?? null ? Schema::contactPoint()->name('facebook')->contactType('socialmedia')->url($data['acf']["link_facebook"]) : null,
                        $data['acf']["link_instagram"] ?? null ? Schema::contactPoint()->name('instagram')->contactType('socialmedia')->url($data['acf']["link_instagram"]) : null,
                    ]
                )
            )
        );
    }

    public function transformNumberOfChildren($school, $data): PreSchool
    {
        return $school->numberOfChildren(
            is_numeric($data['acf']['number_of_children'] ?? null) ? (int)$data['acf']['number_of_children'] : null
        );
    }

    public function transformOpeningHours($school, $data): PreSchool
    {
        return ($data['acf']['open_hours']['open'] ?? null)
        && ($data['acf']['open_hours']['close'] ?? null)
        ?
            $school->openingHoursSpecification(
                Schema::openingHoursSpecification()
                    ->opens($data['acf']['open_hours']['open'] ?? null)
                    ->closes($data['acf']['open_hours']['close'] ?? null)
            )
            : $school;
    }

    public function transformNumberOfGroups($school, $data): PreSchool
    {
        return $school->numberOfGroups(
            (is_numeric($data['acf']['number_of_units'] ?? null) && (int)($data['acf']['number_of_units']) > 0)
                ? (int)($data['acf']['number_of_units'])
                : null
        );
    }


    private function tryCreateTextObject($key, $text): ?TextObject
    {
        if (is_string($key) && is_string($text) && !(empty($key) || empty($text))) {
            return Schema::textObject()
                ->name($key)
                ->headline($this->wellknownTextObjectHeadlinesByKey[$key] ?? $key)
                ->text($text);
        }
        return null;
    }
}
