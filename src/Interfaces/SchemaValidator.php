<?php

namespace SchemaTransformer\Interfaces;

use Municipio\Schema\BaseType;

interface SchemaValidator
{
    public function isValid(BaseType $schema): bool;
}
