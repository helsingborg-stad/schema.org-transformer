<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\AxiellEvents;

use DateTime;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapUrl;
use SchemaTransformer\Transforms\Event\AxiellEvents\Mappers\MapXCreatedBy;

class AxiellEventTransform extends TransformBase implements AbstractDataTransform
{
    private array $excludeTags = ['rådgivning'];
    private string $externalBaseUrl;
    private array $includeTags = [];

    public function __construct(string $idprefix, string $externalBaseUrl, array $excludeTags = [], array $includeTags = [])
    {
        parent::__construct($idprefix);
        $this->externalBaseUrl = $externalBaseUrl;
        $this->excludeTags     = array_map('strtolower', $excludeTags);
        $this->includeTags     = array_map('strtolower', $includeTags);
    }

    public function preprocessData(array $data): array
    {
        // NOTE: HARDCODED RETENTION DATE FOR EVENTS, SHOULD BE REPLACED WITH A CONFIGURABLE OPTION
        $retentionDate = (new DateTime())->sub(new \DateInterval('P1M'))->format('Y-m-d'); // Subtract 1 month from the current date

        $filters = [
            // filter out events that have an end date before the retention date
            fn($item) => ($item['endDate'] ?? '') >= $retentionDate,
            // filter out events that have any of the exclude tags
            fn($item) => count(
                array_intersect(
                    array_map('strtolower', $item['tags'] ?? []),
                    $this->excludeTags
                )
            ) === 0,
            // if includeTags is not empty, filter in only events that have at least one of the include tags
            fn($item) =>
                empty($this->includeTags)
                || count(
                    array_intersect(
                        array_map('strtolower', $item['tags'] ?? []),
                        $this->includeTags
                    )
                ) > 0
        ];

        return array_reduce(
            $filters,
            // apply filter to events
            fn($data, $filter) => array_filter($data, $filter),
            // project $.hits[*].event
            array_map(fn($item) => $item['event'] ?? [], $data['hits'] ?? [])
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
            new MapEventSchedule(),
            new MapOffers(),
            new MapEventStatus(),
            new MapKeywords(),
            new MapPhysicalAccessibilityFeatures(),
            new MapPotentialAction(),
            new MapUrl($this->externalBaseUrl),
            new MapXCreatedBy()
        ];
        $result  = array_map(
            fn ($item) => array_reduce(
                $mappers,
                fn ($event, $mapper) => $mapper->map($event, $item),
                Schema::event()
            )->toArray(),
            $data
        );
        return $result;
    }
}
