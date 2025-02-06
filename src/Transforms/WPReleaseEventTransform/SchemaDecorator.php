<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform;

use Spatie\SchemaOrg\BaseType;

interface SchemaDecorator
{
    public function apply(BaseType $event, array $data): BaseType;
}
