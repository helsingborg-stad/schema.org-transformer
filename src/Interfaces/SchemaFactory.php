<?php

namespace SchemaTransformer\Interfaces;

use Municipio\Schema\BaseType;

interface SchemaFactory
{
    public function createSchema(array $data): BaseType;
}
