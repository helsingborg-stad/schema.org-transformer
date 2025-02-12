<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyIdAsDefinedTermInKeywords implements SchemaDecorator
{
    public function __construct(
        private string $idPrefix
    ) {
    }

    public function apply(BaseType $event, array $data): BaseType
    {
        $originalId = $data['originalId'] ?? $data['id'] ?? null;

        if (empty($originalId)) {
            return $event;
        }

        $keywords   = $event->getProperty('keywords') ?? [];
        $keywords[] = Schema::definedTerm()
            ->name($this->idPrefix . $originalId)
            ->inDefinedTermSet(Schema::definedTermSet()
                ->name('event-ids')
                ->url('https://schema.org/EventIds'));
        return $event->setProperty('keywords', $keywords);
    }
}
