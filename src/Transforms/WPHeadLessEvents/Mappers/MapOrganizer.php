<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\AbstractWPHeadlessEventMapper;

class MapOrganizer extends AbstractWPHeadlessEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        // _embedded->acf:term
            // taxonomy=organization
            // hantera mutipla
        return $event->organizer(
            array_filter([
                $data['acf']['organizerName'] ?? null
                ? Schema::organization()
                    ->name($data['acf']['organizerName'] ?? null)
                    ->telephone($data['acf']['organizerPhone'] ?? null)
                    ->email($data['acf']['organizerEmail'] ?? null)
                    ->address($data['acf']['organizerAddress'] ?? null)
                    ->url($data['acf']['organizerUrl'] ?? null)
                : null
            ])
        );
    }
}
