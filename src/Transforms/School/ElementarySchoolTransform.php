<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Typesense\Client as TypesenseClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClient;
use SchemaTransformer\Transforms\School\Events\EventsSearchClientOnTypesense;
use SchemaTransformer\Transforms\School\Events\NullEventsSearchClient;
use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\Place;
use Municipio\Schema\TextObject;

class ElementarySchoolTransform implements AbstractDataTransform
{
    private array $wellknownTextObjectHeadlinesByKey = [
        'about_us'           => 'Om oss',
        'how_we_work'        => 'Hur vi arbetar',
        'our_leisure_center' => 'Vår fritidsverksamhet',
        // 'our_mission'        => 'Vår mission',
        // 'our_vision'         => 'Vår vision',
        // 'our_values'         => 'Våra värderingar',
        // 'history'            => 'Historia',
        // 'extra'              => 'Extra information'
    ];

    private EventsSearchClient $eventsSearchClient;

    /**
     * ElementarySchoolTransform constructor.
     */
    public function __construct(private ?TypesenseClient $typesenseClient = null)
    {
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
        $transformations = [
            'transformBase',
            'transformDescription',
            'transformPlace',
            'transformEvents'
        ];

        // TODO:
        // - usp: ['_embedded']['acf:term']['name'] => keywords
        // - ansökningar: ['cta_application']['cta_xxx'] =>
        return array_map(function ($item) use ($transformations) {
            return array_reduce(
                $transformations,
                function ($school, $method) use ($item) {
                    return $this->$method($school, $item);
                },
                Schema::elementarySchool()
            )->toArray();
        }, $data);
    }

    public function transformBase($school, $data): ElementarySchool
    {
        return $school
                ->identifier((string)$data['id'])
                ->name($data['title']['rendered'] ?? null);
    }

    public function transformDescription($school, $data): ElementarySchool
    {
        return $school
                ->description($this->getDescription($data));
    }

    public function transformPlace($school, $data): ElementarySchool
    {
        return $school
                ->location($this->getPlace($data))
                // ElementarySchool is a Place also
                ->addProperties(
                    $this->getPlace($data)->toArray()
                );
    }

    public function transformEvents(ElementarySchool $school, $data): ElementarySchool
    {
        return $school->event(
            $this->eventsSearchClient->searchEventsBySchoolName($school->getProperty('name') ?? '')
        );
    }

    private function getPlace($dataItem): ?Place
    {
        foreach ($dataItem['acf']['visiting_address'] as $address) {
            $a = $address['address'];
            return Schema::place()
                ->name($a['name'] ?? null)
                ->address($a['address'] ?? null)
                ->latitude($a['lat'] ?? null)
                ->longitude($a['lng'] ?? null);
        }
        return null;
    }

    private function getDescription($dataItem): array
    {
        $a = array(
            $dataItem['acf']['custom_excerpt']);

        foreach ($dataItem['acf']['information'] ?? [] as $key => $text) {
            $to =
                (
                    is_string($text) ? $this->tryCreateTextObject($key, $text) : null
                ) ?? (
                    is_array($text) && is_array($text[0]) ?
                    $this->tryCreateTextObject($text[0]['heading'], $text[0]['content']) : null
                );
            if ($to) {
                array_push($a, $to);
            }
        }
        return $a;
    }

    private function tryCreateTextObject($key, $text): ?TextObject
    {
        if (is_string($key) && is_string($text) && !(empty($key) || empty($text))) {
            return
                Schema::textObject()
                ->name($key)
                ->headline($this->wellknownTextObjectHeadlinesByKey[$key] ?? $key)
                ->text($text);
        }
        return null;
    }
}
