<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyIsAccessibleForFree implements SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType
    {
        if ($this->isFreeEvent($data)) {
            return $event->setProperty('isAccessibleForFree', true);
        }

        return $event;
    }

    private function isFreeEvent(array $data): bool
    {
        return $data['acf']['pricing'] === 'free';
    }
}
