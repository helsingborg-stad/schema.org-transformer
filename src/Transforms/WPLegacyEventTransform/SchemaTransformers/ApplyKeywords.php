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

        return $event->setProperty('keywords', [...$eventCategories, ...$eventTags, ...$accessibilityTerms]);
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
