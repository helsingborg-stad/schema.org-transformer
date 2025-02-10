<?php

namespace SchemaTransformer\Interfaces;

use Spatie\SchemaOrg\BaseType;

interface SchemaValidator
{
    public function isValid(BaseType $schema): bool;
}
