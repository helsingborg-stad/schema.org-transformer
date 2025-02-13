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
            ->name($organizer['organizer'] ?? null)
            ->url($organizer['organizer_link'] ?? null)
            ->email($organizer['organizer_email'] ?? null)
            ->telephone($organizer['organizer_phone'] ?? null));
    }

    private function dataContainsOrganizer(array $data): bool
    {
         return !empty($data['organizers']);
    }

    private function getOrganizer(array $data): ?array
    {
        return $data['organizers'][0] ?? null;
    }
}
