<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Preschool;

class MapKeywords extends AbstractPreSchoolDataMapper
{
    private array $taxonomiesExcludedFromKeywords = [
        'area'  => true,
        'grade' => true,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
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
}
