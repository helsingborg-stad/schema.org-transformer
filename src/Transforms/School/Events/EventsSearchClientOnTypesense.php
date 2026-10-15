<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\School\Events;

use SchemaTransformer\Storage\TypesenseStorage\TypesenseCollection;
use Typesense\Client as TypesenseClient;

use function PHPUnit\Framework\stringContains;

class EventsSearchClientOnTypesense implements EventsSearchClient
{
    public function __construct(private TypesenseClient $typesenseClient)
    {
    }

    public function jsonQuoteFilterValue(string $value): string
    {
        // $encodedValue = json_encode(
        //     $value,
        //     JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_THROW_ON_ERROR
        // );
        // return '`' . substr($encodedValue, 1, -1) . '`';

        return '`' . $value . '`';
    }

    public function createFilterBy($field, array $values): ?string
    {
        $sanitizedValues = array_values(
            array_filter(
                array_map(
                    fn($q) => is_string($q) && !empty($q) && !preg_match('/[<>\'\"&`]/', $q) ? trim($q) : null,
                    $values
                )
            )
        );
        if (empty($sanitizedValues)) {
            return null;
        }
        if (count($sanitizedValues) === 1) {
            return $field . ':' . $this->jsonQuoteFilterValue($sanitizedValues[0]);
        }

        $qlist = implode(
            ",",
            array_filter(
                array_values(
                    array_map(
                        fn($q) => is_string($q) && !empty($q) ? $this->jsonQuoteFilterValue($q) : null,
                        $sanitizedValues
                    )
                )
            )
        );
        return $field . ':[' . $qlist . ']';
    }

    public function createTypesenseQuery(string $schoolName, array $eventOrganizations): ?array
    {
        $filters = array_values(array_filter([
            $this->createFilterBy('keywords.name', [$schoolName]),
            $this->createFilterBy('organizer.name', $eventOrganizations)
        ]));
        if (empty($filters)) {
            return null;
        }
        return [
            'q'         => '*',
            'query_by'  => 'organizer.name,keywords.name',
            'filter_by' => implode(' || ', $filters),
        ];
    }

    public function searchEventsBySchool(string $schoolName, array $eventOrganizations): array
    {
        $query = $this->createTypesenseQuery($schoolName, $eventOrganizations);
        if ($query === null) {
            return [];
        }

        $result = $this->typesenseClient->collections[TypesenseCollection::Event->value]->documents->search($query);
        return array_values(array_filter(array_map(
            function ($hit) {
                return $hit['document'];
            },
            $result['hits'] ?? []
        )));
    }
}
