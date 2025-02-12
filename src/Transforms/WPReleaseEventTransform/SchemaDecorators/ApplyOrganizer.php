<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyOrganizer implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (!$this->dataContainsOrganizer($data)) {
            return $event;
        }

        $organizerTerm = $this->getOrganizerTerm($data);

        return $event->setProperty('organizer', Schema::organization()
            ->name($organizerTerm['name'] ?? null)
            ->url($organizerTerm['acf']['url'] ?? null)
            ->email($organizerTerm['acf']['email'] ?? null)
            ->telephone($organizerTerm['acf']['telephone'] ?? null)
            ->address($organizerTerm['acf']['address']['address'] ?? null));
    }

    private function dataContainsOrganizer(array $data): bool
    {
        if (!isset($data['_embedded']['wp:term'])) {
            return false;
        }

        foreach ($data['_embedded']['wp:term'] as $taxonomy) {
            foreach ($taxonomy as $term) {
                if ($term['taxonomy'] === 'organization') {
                    return true;
                }
            }
        }

        return false;
    }

    private function getOrganizerTerm(array $data): array
    {
        foreach ($data['_embedded']['wp:term'] as $taxonomy) {
            foreach ($taxonomy as $term) {
                if ($term['taxonomy'] === 'organization') {
                    return $term;
                }
            }
        }
    }
}
