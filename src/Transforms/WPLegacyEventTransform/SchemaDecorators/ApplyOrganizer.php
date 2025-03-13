<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class ApplyOrganizer implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($organizers = $this->getOrganizers($data))) {
            return $event;
        }

        $organizerSchemas = array_map(function ($organizer) {
            return Schema::organization()
                ->name($organizer['title']['rendered'] ?? null)
                ->url($organizer['website'] ?? null)
                ->email($organizer['email'] ?? null)
                ->telephone($organizer['phone'] ?? null);
        }, $organizers);

        return $event->setProperty('organizer', $organizerSchemas);
    }

    private function getOrganizers(array $data): array
    {
        return $data['_embedded']['organizers'] ?? [];
    }
}
