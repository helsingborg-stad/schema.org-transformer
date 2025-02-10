<?php

namespace SchemaTransformer\Transforms\WPLegacyEventTransform;

use SchemaTransformer\Interfaces\SchemaFactory;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class EventFactory implements SchemaFactory
{
    public function createSchema(array $data): BaseType
    {
        return Schema::event();
    }
}
