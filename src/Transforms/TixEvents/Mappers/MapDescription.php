<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents\Mappers;

use Municipio\Schema\Event;

class MapDescription extends AbstractTixDataMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->description(
                    array_values(
                        array_filter(
                            [$data['SubTitle'] ?? null]
                        )
                    )
                );
    }
}
