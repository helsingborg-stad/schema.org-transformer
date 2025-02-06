<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyDescription implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        return $event->setProperty('description', $data['acf']['description'] ?? null);
    }
}
