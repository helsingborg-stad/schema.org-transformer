<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyOrganizer implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $organizer = $this->getOrganizer($data);

        if (!$this->dataContainsOrganizer($data) || !$organizer) {
            return $event;
        }

        return $event->setProperty('organizer', Schema::organization()
            ->name($organizer['title']['rendered'] ?? null)
            ->url($organizer['website'] ?? null)
            ->email($organizer['email'] ?? null)
            ->telephone($organizer['phone'] ?? null));
    }

    private function dataContainsOrganizer(array $data): bool
    {
         return !empty($data['_embedded']['organizers']);
    }

    private function getOrganizer(array $data): array
    {
        return $data['_embedded']['organizers'][0] ?? null;
    }
}
