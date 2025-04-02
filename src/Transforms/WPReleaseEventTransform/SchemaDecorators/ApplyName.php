<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorators;

use SchemaTransformer\Interfaces\PathValueAccessor;
use SchemaTransformer\Transforms\WPReleaseEventTransform\SchemaDecorator;
use Municipio\Schema\BaseType;

class ApplyName implements SchemaDecorator
{
    public function __construct(
        private string $valuePath,
        private PathValueAccessor $pathValueAccessor
    ) {
    }

    public function apply(BaseType $event, array $data): BaseType
    {
        return $event->setProperty('name', $this->pathValueAccessor->getValue($data, $this->valuePath));
    }
}
