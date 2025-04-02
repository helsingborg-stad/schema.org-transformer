<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyUrl implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if (empty($data['event_link'])) {
            return $event;
        }

        return $event->setProperty('url', $data['event_link']);
    }
}
