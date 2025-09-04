<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

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
            return Schema::elementarySchool()
                ->identifier((string)$item['id'])
                ->name($item['title']['rendered'] ?? null)
                ->description($this->getDescription($item))

                ->location($this->getPlace($item))

                // ElementarySchool is a Place also
                ->addProperties(
                    $this->getPlace($item)->toArray()
                )
                ->toArray();
        }, $data);
    }

    private function getPlace($dataItem): ?Place
    {
        foreach ($dataItem['acf']['visiting_address'] as $address) {
            $a = $address['address'];
            return Schema::place()
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

        array_walk_recursive($dataItem['acf']['information'], function ($text, $key) use (&$a) {
            array_push(
                $a,
                Schema::textObject()
                    ->name($key)
                    ->text($text)
            );
        });
        return $a;
    }
}
