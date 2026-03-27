<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\Event;

class MapKeywords extends AbstractAxiellEventsDataMapper
{
    public function map(Event $event, array $data): Event
    {
        return $event->keywords(
            array_values(
                array_map(
                    fn($tag) =>
                    Schema::definedTerm()
                        ->name($tag)
                        ->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
                    $data['tags'] ?? []
                )
            )
        );
    }
}
