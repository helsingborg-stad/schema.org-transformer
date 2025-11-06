<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\PreSchool\Mappers;

use Municipio\Schema\Preschool;

class MapDescription extends AbstractPreSchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Preschool $school, array $data): Preschool
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
}
