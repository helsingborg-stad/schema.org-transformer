<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyEventStatus implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $status = $data['occasions'][0]['status'] ?? null;

        if (empty($status)) {
            return $event;
        }

        $status = $this->transformStringToStatusEnumeration($status);
        return $event->eventStatus($status);
    }

    private function transformStringToStatusEnumeration(string $status): string
    {
        return match (strtolower($status)) {
            'rescheduled' => Schema::eventStatusType()::EventRescheduled,
            'cancelled' => Schema::eventStatusType()::EventCancelled,
            default => Schema::eventStatusType()::EventScheduled,
        };
    }
}
