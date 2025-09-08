<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Typesense\Client as TypesenseClient;
use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\Place;
use Municipio\Schema\TextObject;

class ElementarySchoolTransform implements AbstractDataTransform
{
    /**
     * ElementarySchoolTransform constructor.
     */
    public function __construct(private ?TypesenseClient $typesenseClient = null)
    {
    }

    public function transform(array $data): array
    {
        $transformations = [
            'transformBase',
            'transformDescription',
            'transformPlace',
            'transformEvents'
        ];

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
        if (!$this->typesenseClient) {
            return $school;
        }

        // We perform one events search per school
        // - limited by page size
        // This could be optimized by batching searches if needed
        // - requires paginated reads
        // - required caching of first (large and complete) batch
        $x = $this->typesenseClient->collections['events']->documents->search([
            'q'        => '"' . $school->getProperty('name') . '"',
            'query_by' => 'keywords.name',
        ]);

        // $x['hits'][index]['document'] conains a wellformed event object (albeit not a constructed Schema::event)
        $school->event(array_map(
            function ($hit) {
                return $hit['document'];
            },
            $x['hits'] ?? []
        ));
        return $school;
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
                ->text($text);
        }
        return null;
    }
}
