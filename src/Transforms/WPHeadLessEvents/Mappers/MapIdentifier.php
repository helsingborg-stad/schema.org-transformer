<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use SchemaTransformer\Transforms\TransformBase;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapIdentifier extends AbstractWPHeadlessEventMapper
{
    public function __construct(private TransformBase $transform)
    {
        parent::__construct($transform);
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->identifier(
                    empty($data['id'])
                    ? null
                    : $this->formatId((string)$data['id'] ?? '')
                );
    }
}
