<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Event;

interface WPLegacyEventMapperInterface
{
    public function map(Event $event, array $data): Event;
}
