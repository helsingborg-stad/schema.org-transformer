<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;

interface WPHeadlessEventMapperInterface
{
    public function map(Event $event, array $data): Event;
}
