<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;
use SchemaTransformer\Transforms\School\Events\NullEventsSearchClient;
use Municipio\Schema\PreSchool;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\TextObject;

class PreSchoolTransform implements AbstractDataTransform
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'custom_excerpt'     => '',
        'visit_us'           => 'Besök oss',
        'about_us'           => 'Om oss',
        'how_we_work'        => 'Hur vi arbetar',
        'our_leisure_center' => 'Vår fritidsverksamhet',
        // 'our_mission'        => 'Vår mission',
        // 'our_vision'         => 'Vår vision',
        // 'our_values'         => 'Våra värderingar',
        // 'history'            => 'Historia',
        // 'extra'              => 'Extra information'
    ];

    private array $taxonomiesExcludedFromKeywords = [
        'area' => true,
    ];

    private EventsSearchClient $eventsSearchClient;

    /**
     * PreSchoolTransform constructor.
     */
    public function __construct(private ?TypesenseClient $typesenseClient = null)
    {
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
        // TODO:
        // - acf::open_hours -> openingHoursSpecification
        // - acf::number_of_units antal avdelningar


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
        return $school
            ->description(array_values(array_filter($descriptions)));
    }

    public function transformPlace($school, $data): PreSchool
    {
        foreach (($data['acf']['visiting_address'] ?? []) as $address) {
            $a     = $address['address'];
            $place = Schema::place()
                ->name($a['name'] ?? null)
                ->address($a['address'] ?? null)
                ->latitude($a['lat'] ?? null)
                ->longitude($a['lng'] ?? null);

            return $school
                ->location($place)
                // PreSchool is a Place also
                ->addProperties(
                    array_filter($place->toArray(), fn($key) => $key !== 'name', ARRAY_FILTER_USE_KEY)
                );
        }
        return $school;
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
