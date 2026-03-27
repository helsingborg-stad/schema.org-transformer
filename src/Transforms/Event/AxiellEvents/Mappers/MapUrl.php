<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Event;

class MapUrl extends AbstractAxiellEventsDataMapper
{
    private string $externalBaseUrl;

    public function __construct(string $externalBaseUrl)
    {
        parent::__construct();
        $this->externalBaseUrl = $externalBaseUrl;
    }

    public function map(Event $event, array $data): Event
    {
        return empty($this->externalBaseUrl)
            ? $event
            : $event->url($this->externalBaseUrl . '/evenemang#/events/' . ($data['id'] ?? ''))
        ;
    }
}
