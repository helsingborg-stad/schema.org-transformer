<?php

namespace SchemaTransformer\Interfaces;

use Spatie\SchemaOrg\BaseType;

interface SchemaFactory
{
    public function createSchema(array $data): BaseType;
}
