<?php

namespace SchemaTransformer\Transforms\Validators;

use SchemaTransformer\Interfaces\SchemaValidator;
use Municipio\Schema\BaseType;

class EventValidator implements SchemaValidator
{
    public function isValid(BaseType $schema): bool
    {
        if (is_null($schema->getProperty('identifier'))) {
            return false;
        }

        if (is_null($schema->getProperty('name'))) {
            return false;
        }

        if (is_null($schema->getProperty('startDate'))) {
            return false;
        }

        return true;
    }
}
