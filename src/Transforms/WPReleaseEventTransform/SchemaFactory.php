<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform;

use Spatie\SchemaOrg\BaseType;

interface SchemaFactory
{
    public function createSchema(array $data): BaseType;
}
