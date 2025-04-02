<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform;

use Municipio\Schema\BaseType;

interface SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType;
}
