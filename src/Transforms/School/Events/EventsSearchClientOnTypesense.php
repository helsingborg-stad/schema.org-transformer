<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

use Typesense\Client as TypesenseClient;

class EventsSearchClientOnTypesense implements EventsSearchClient
{
    public function __construct(private TypesenseClient $typesenseClient)
    {
    }
    public function searchEventsBySchoolName(string $schoolName): array
    {
        if (empty($schoolName)) {
            return [];
        }

        $x = $this->typesenseClient->collections['events']->documents->search([
            'q'        => '"' . $schoolName . '"',
            'query_by' => 'keywords.name',
        ]);

        // $x['hits'][index]['document'] contains a wellformed event object (albeit not a constructed Schema::event)
        return array_map(
            function ($hit) {
                return $hit['document'];
            },
            $x['hits'] ?? []
        );
    }
}
