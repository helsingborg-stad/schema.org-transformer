<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Contracts\AudienceContract;
use Spatie\SchemaOrg\Schema;

class ApplyAudience implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $termNames = $this->getTermsAsArrayOfStrings($data, 'audience');

        if (empty($termNames)) {
            return $event;
        }

        $audiences = array_map([$this, 'getAudienceFromString'], $termNames);

        return $event->setProperty('audience', $audiences);
    }

    private function getAudienceFromString(string $audienceName): AudienceContract
    {
        return Schema::audience()->audienceType($audienceName);
    }

    private function getTermsAsArrayOfStrings(array $data, string $taxonomy): array
    {
        $terms = $this->getTermsFromRow($data, $taxonomy);
        return array_map(fn($term) => $term['name'], $terms);
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
