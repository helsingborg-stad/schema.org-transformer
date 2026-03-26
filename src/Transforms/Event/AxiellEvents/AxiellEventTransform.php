<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventAttendanceMode;
// use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapLocation;
// use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventStatus;
// use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapKeywords;
// use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapXCreatedBy;

class AxiellEventTransform extends TransformBase implements AbstractDataTransform
{
    private array $excludeTags = ['rådgivning'];

    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
    }

    public function preprocessData(array $data): array
    {
        // Implement any necessary preprocessing steps here


        return array_filter(
            // project $.hits[*].event
            array_map(fn($item) => $item['event'] ?? [], $data['hits'] ?? []),
            // filter out events that have any of the exclude tags
            fn($item) => count(
                array_intersect(
                    array_map('strtolower', $item['tags'] ?? []),
                    $this->excludeTags
                )
            ) === 0
        );
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapIdentifier($this),
            new MapName(),
            new MapDescription(),
            new MapIsAccessibleForFree(),
            new MapEventAttendanceMode(),
            new MapStartDate(),
            new MapEndDate(),
            new MapOrganizer(),
            new MapLocation(),
            new MapImage(),
            // new MapEventSchedule($this),
            // new MapOffers(false), // Do not include products in offers
            new MapEventStatus(),
            // new MapKeywords(),
            // new MapPhysicalAccessibilityFeatures(),
            new MapXCreatedBy()
        ];
        $result  = array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($event, $mapper) use ($item) {
                    return $mapper->map($event, $item);
                },
                Schema::event()
            )->toArray();
        }, $data);
        return $result;
    }
}
