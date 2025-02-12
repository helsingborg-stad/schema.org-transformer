<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\DefinedTermContract;
use Spatie\SchemaOrg\Schema;

class ApplyKeywords implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $terms    = $this->getTermsFromRow($data);
        $terms    = array_filter($terms, fn($term) => !empty($term['taxonomy']));
        $keywords = array_map([$this, 'getDefinedTermFromTerm'], $terms);

        if (is_array($event->getProperty('keywords'))) {
            $keywords = array_merge($event->getProperty('keywords'), $keywords);
        }

        return $event->setProperty('keywords', $keywords);
    }

    private function getDefinedTermFromTerm(array $term): DefinedTermContract
    {
        return Schema::definedTerm()
            ->name($term['name'])
            ->inDefinedTermSet(Schema::definedTermSet()->name($term['taxonomy']));
    }

    private function getTermsFromRow(array $data): array
    {
        $result     = [];
        $taxonomies = $data['_embedded']['wp:term'] ?? [];

        if (empty($taxonomies)) {
            return [];
        }

        foreach ($taxonomies as $terms) {
            foreach ($terms as $term) {
                $result[] = $term;
            }
        }

        return $result;
    }
}
