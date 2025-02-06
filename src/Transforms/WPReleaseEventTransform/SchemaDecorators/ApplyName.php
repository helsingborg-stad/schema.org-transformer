<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Spatie\SchemaOrg\BaseType;

class ApplyName implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        $title = $data['title']['rendered'] ?? null;

        if (!empty($title)) {
            return $event->setProperty('name', $title);
        }

        return $event;
    }
}
