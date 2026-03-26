<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers;

use Municipio\Schema\Event;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapName extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event
                ->name($data['title']['rendered'] ?? null);
    }
}
