<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\ElementarySchool;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use Municipio\Schema\Schema;
use Municipio\Schema\Place;

class ElementarySchoolTransform implements AbstractDataTransform
{
    /**
     * ElementarySchoolTransform constructor.
     */
    public function __construct()
    {
    }

    public function transform(array $data): array
    {
        return array_map(function ($item) {
            return $this->transformDescription(
                $this->transformPlace(
                    $this->transformBase(
                        Schema::elementarySchool(),
                        $item
                    ),
                    $item
                ),
                $item
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

        function tryCreateTextObject($key, $text)
        {
            if (is_string($key) && is_string($text) && !(empty($key) || empty($text))) {
                return
                    Schema::textObject()
                    ->name($key)
                    ->text($text);
            }
            return null;
        }

        foreach ($dataItem['acf']['information'] ?? [] as $key => $text) {
            $to =
                (
                    is_string($text) ? tryCreateTextObject($key, $text) : null
                ) ?? (
                    is_array($text) && is_array($text[0]) ?
                    tryCreateTextObject($text[0]['heading'], $text[0]['content']) : null
                );
            if ($to) {
                array_push($a, $to);
            }
        }
        return $a;
    }
}
