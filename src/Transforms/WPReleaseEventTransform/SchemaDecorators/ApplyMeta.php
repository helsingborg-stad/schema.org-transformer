<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyMeta implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        return $event->setProperty('@meta', [
            ...$this->getPropertyValuesFromTerms($data, 'physical-accessibility'),
            ...$this->getPropertyValuesFromTerms($data, 'cognitive-accessibility')
        ]);
    }

    private function getPropertyValuesFromTerms(array $data, $taxonomy): array
    {
        $terms = $this->getTermsFromRow($data, $taxonomy);
        return array_map(fn($term) => Schema::propertyValue() ->name($term['taxonomy']) ->value($term['name']), $terms);
    }

    private function getTermsFromRow(array $data, string $taxonomy): array
    {
        $result     = [];
        $taxonomies = $data['_embedded']['wp:term'] ?? [];

        if (empty($taxonomies)) {
            return [];
        }

        foreach ($taxonomies as $terms) {
            foreach ($terms as $term) {
                if ($term['taxonomy'] === $taxonomy) {
                    $result[] = $term;
                }
            }
        }

        return $result;
    }
}
