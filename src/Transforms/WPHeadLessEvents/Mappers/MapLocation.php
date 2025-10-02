<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapLocation extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->location(
                    array_filter([
                        empty($data['acf']['locationAddress']['name'] ?? null)
                            ? null
                            : Schema::place()
                                ->name($data['acf']['locationAddress']['name'] ?? null)
                                ->address($data['acf']['locationAddress']['address'] ?? null)
                                ->latitude($data['acf']['locationAddress']['lat'] ?? null)
                                ->longitude($data['acf']['locationAddress']['lng'] ?? null)
                                ->url($data['acf']['locationName'] ?? null),
                    ])
                );
    }
}
