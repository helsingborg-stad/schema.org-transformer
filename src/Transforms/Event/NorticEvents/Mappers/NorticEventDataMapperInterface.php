<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;

interface NorticEventDataMapperInterface
{
    public function map(Event $event, array $data): Event;
}
