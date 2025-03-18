<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyKeywords implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $eventCategories    = $this->getDefinedTerms('event_categories', 'event_categories', $data);
        $eventTags          = $this->getDefinedTerms('event_tags', 'event_tags', $data);
        $accessibilityTerms = $this->getDefinedTerms('accessibility', 'physical-accessibility', $data);
        $userGroups         = $this->getDefinedTermsFromArrayOfTerms('user_groups', 'user_groups', $data);

        $accessibilityTerms = $this->mapAccesibilityTermNames($accessibilityTerms);

        return $event->setProperty('keywords', [...$eventCategories, ...$eventTags, ...$userGroups, ...$accessibilityTerms]);
    }

    private function mapAccesibilityTermNames($terms): array
    {
        $map = ['Accessible toilet' => 'Handikapptoalett', 'Elevator/ramp' => 'Hiss/ramp'];

        foreach ($terms as $term) {
            $term->name($map[$term->getProperty('name')] ?? $term->getProperty('name'));
        }

        return $terms;
    }


    private function getDefinedTermsFromArrayOfTerms(string $dataPath, string $taxonomy, array $data): array
    {
        $result = [];

        if (empty($data[$dataPath])) {
            return [];
        }

        foreach ($data[$dataPath] as $value) {
            if (!is_array($value) || !isset($value['name'])) {
                continue;
            }

            $result[] = Schema::definedTerm()
                ->name($value['name'])
                ->inDefinedTermSet(Schema::definedTermSet()->name($taxonomy));
        }

        return $result;
    }

    private function getDefinedTerms(string $dataPath, string $taxonomy, array $data): array
    {
        $result = [];

        if (empty($data[$dataPath])) {
            return [];
        }

        foreach ($data[$dataPath] as $value) {
            $result[] = Schema::definedTerm()
                ->name($value)
                ->inDefinedTermSet(Schema::definedTermSet()->name($taxonomy));
        }

        return $result;
    }
}
