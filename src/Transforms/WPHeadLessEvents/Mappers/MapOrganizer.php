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
        return $event->organizer(
            array_values(
                array_filter(
                    array_map(
                        fn ($term) =>
                            ($term['taxonomy'] ?? null) === 'organization'
                                ? Schema::organization()
                                    ->name($term['name'] ?? null)
                                    ->address($term['acf']['address'] ?? null)
                                    ->url($term['acf']['url'] ?? null)
                                    ->email($term['acf']['email'] ?? null)
                                    ->telephone($term['acf']['telephone'] ?? null)
                                    ->contactPoint(
                                        isset($term['acf']['contact'])
                                            ? [Schema::contactPoint()
                                                ->name($term['acf']['contact'] ?? null)]
                                        : []
                                    )
                                    : null,
                        ($data['_embedded']['acf:term'] ?? []),
                    )
                )
            )
        );
    }
}
