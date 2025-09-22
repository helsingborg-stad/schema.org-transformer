<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\TransformBase;

class MapName extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
            ->name($data['SubTitle'] ?? null);
    }
}
