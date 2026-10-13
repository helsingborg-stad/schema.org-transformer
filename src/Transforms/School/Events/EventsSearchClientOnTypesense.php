<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use Typesense\Client as TypesenseClient;

class EventsSearchClientOnTypesense implements EventsSearchClient
{
    public function __construct(private TypesenseClient $typesenseClient)
    {
    }

    public function escapeAndQuoteQuery(string $query): string
    {
        return '"' . preg_replace('/([\\\\"])/', '\\\\$1', $query) . '"';
    }
    public function searchEventsBySchoolName(string $schoolName): array
    {
        if (empty($schoolName)) {
            return [];
        }

        $result = $this->typesenseClient->collections[TypesenseCollection::Event->value]->documents->search([
            'q'        => $this->escapeAndQuoteQuery($schoolName),
            'query_by' => 'organizer.name, keywords.name',
        ]);

        // $result['hits'][index]['document'] contains a wellformed event object (albeit not a constructed Schema::event)
        return array_values(array_filter(array_map(
            function ($hit) {
                return $hit['document'];
            },
            $result['hits'] ?? []
        )));
    }
}
