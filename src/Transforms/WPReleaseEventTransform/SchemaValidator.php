<?php

namespace SchemaTransformer\Transforms\WPReleaseEventTransform;

use Spatie\SchemaOrg\BaseType;

interface SchemaValidator
{
    public function isValid(BaseType $schema): bool;
}
