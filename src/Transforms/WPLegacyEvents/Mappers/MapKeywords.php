<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents\Mappers;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\AbstractWPLegacyEventMapper;
use Municipio\Schema\Event;

class MapKeywords extends AbstractWPLegacyEventMapper
{
    public function __construct()
    {
        parent::__construct();
    }

    public function map(Event $event, array $data): Event
    {
        return $event->keywords([
        ...array_map(
            fn ($term) => Schema::definedTerm()
                    ->name($term)
                    ->inDefinedTermSet(Schema::definedTermSet()->name('event_categories')),
            $data['event_categories'] ?? []
        ),
        ...array_map(
            fn ($term) => Schema::definedTerm()
                    ->name($term)
                    ->inDefinedTermSet(Schema::definedTermSet()->name('event_tags')),
            $data['event_tags'] ?? []
        ),
        ...array_map(
            fn ($term) => Schema::definedTerm()
                    ->name($term)
                    ->inDefinedTermSet(Schema::definedTermSet()->name('user_groups')),
            array_values(
                array_filter(
                    array_map(
                        fn ($ug) => $ug['name'] ?? '',
                        $data['user_groups'] ?? []
                    )
                )
            )
        )
        ]);
    }
}
