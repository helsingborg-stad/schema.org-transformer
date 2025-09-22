<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

class MapIdentifier extends AbstractTixDataMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->identifier(
                    $this->formatId((string)$data['EventGroupId'] ?? '')
                );
    }
}
