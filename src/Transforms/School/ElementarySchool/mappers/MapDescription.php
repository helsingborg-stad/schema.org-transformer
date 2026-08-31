<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\ElementarySchool\Mappers;

use Municipio\Schema\ElementarySchool;

class MapDescription extends AbstractElementarySchoolDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(ElementarySchool $school, array $data): ElementarySchool
    {
        $descriptions = [
            $this->tryCreateTextObject('role:preamble', $data['acf']['custom_excerpt'] ?? null),
            $this->tryCreateTextObjectWithHeadline('role:alert', $data['acf']['notice_content'] ?? null, $data['acf']['notice_heading'] ?? null),
            $this->tryCreateTextObject('visit_us', $data['acf']['visit_us'] ?? null),
        ];

        foreach ($data['acf']['information'] ?? [] as $key => $text) {
            if (is_string($text)) {
                $descriptions[] = $this->tryCreateTextObject($key, $text);
            } elseif (is_array($text)) {
                foreach ($text as $item) {
                    $descriptions[] = $this->tryCreateTextObject($item['heading'], $item['content']);
                }
            }
        }
        foreach ($data['pages_embedded'] ?? [] as $page) {
            array_push($descriptions, $this->tryCreateTextObject($page['post_title'] ?? null, $page['post_content'] ?? null));
        }

        return $school
            ->description(array_values(array_filter($descriptions)));
    }
}
