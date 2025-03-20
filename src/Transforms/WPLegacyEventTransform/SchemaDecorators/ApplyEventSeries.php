<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyEventSeries implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($data['eventsInSameSeries'])) {
            return $event;
        }

        return $event->eventsInSameSeries($data['eventsInSameSeries']);
    }
}
