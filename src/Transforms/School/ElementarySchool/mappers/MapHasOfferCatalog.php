<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\Schema;
use Municipio\Schema\ElementarySchool;

class MapHasOfferCatalog extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        $grades = array_values(array_filter(
            array_map(
                fn ($t) =>
                        !empty($t) && is_string($t['name'] ?? null) && !empty($t['name'] ?? null) && ($t['taxonomy'] ?? null) === 'grade'
                        ? Schema::listItem()
                            ->name($t['name'])
                            ->description($t['name'])
                        : null,
                ($data['_embedded']['acf:term'] ?? [])
            )
        ));

        return !empty($grades)
            ? $school->hasOfferCatalog(
                [Schema::offerCatalog()
                    ->name('Årskurser')
                    ->description('Årskurser som skolan erbjuder')
                    ->itemListElement($grades)
                ]
            )
            : $school->hasOfferCatalog([]);
    }
}
