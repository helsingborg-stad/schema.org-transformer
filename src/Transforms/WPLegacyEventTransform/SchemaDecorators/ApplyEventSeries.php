<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;

class ApplyEventSeries implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($data['eventsInSameSeries'])) {
            return $event;
        }

        return $event->addProperties(['eventsInSameSeries' => [
            ...array_map(fn($id) => Schema::event()->identifier($id), $data['eventsInSameSeries'])
        ]]);
    }
}
