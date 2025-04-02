<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyEventStatus implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        return $event->eventStatus($data['acf']['eventStatus'] ?? null);
    }
}
