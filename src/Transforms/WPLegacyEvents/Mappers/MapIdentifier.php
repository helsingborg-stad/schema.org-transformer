<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapIdentifier extends AbstractWPLegacyEventMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->identifier(
                    $this->formatId((string)$data['id'] ?? '')
                );
    }
}
