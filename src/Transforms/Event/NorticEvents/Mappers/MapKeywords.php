<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents\Mappers;

use Municipio\Schema\Event;
use Municipio\Schema\Schema;

class MapKeywords extends AbstractNorticEventDataMapper
{
    public function map(Event $event, array $data): Event
    {
        $tags = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));
        if (!empty($data['category'])) {
            $tags[] = $data['category'];
        }

        return $event->keywords(array_map(
            fn (string $tag) => Schema::definedTerm()
                ->name($tag)
                ->inDefinedTermSet(Schema::definedTermSet()->name('tags')),
            array_values(array_unique($tags))
        ));
    }
}
