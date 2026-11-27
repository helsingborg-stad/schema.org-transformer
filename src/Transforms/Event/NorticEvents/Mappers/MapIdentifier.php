<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

class MapIdentifier extends AbstractNorticEventDataMapper
{
    public function __construct(TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Event $event, array $data): Event
    {
        return $event->identifier($this->formatId($data['id'] ?? ''));
    }
}
