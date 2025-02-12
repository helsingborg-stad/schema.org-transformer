<?php

namespace SchemaTransformer\Transforms\Validators;

use SchemaTransformer\Interfaces\SchemaValidator;
use Spatie\SchemaOrg\BaseType;

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

        if (is_null($schema->getProperty('startDate')) || $schema->getProperty('startDate') < date('Y-m-d')) {
            return false;
        }

        return true;
    }
}
