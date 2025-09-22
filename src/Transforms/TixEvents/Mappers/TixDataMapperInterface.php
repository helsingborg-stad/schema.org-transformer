<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;

interface TixDataMapperInterface
{
    public function map(Event $event, array $data): Event;
}
