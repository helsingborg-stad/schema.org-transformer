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

    public function __construct(string $idprefix, string $externalBaseUrl)
    {
        parent::__construct($idprefix);
        $this->externalBaseUrl = $externalBaseUrl;
    }

    public function preprocessData(array $data): array
    {
        // NOTE: HARDCODED RETENTION DATE FOR EVENTS, SHOULD BE REPLACED WITH A CONFIGURABLE OPTION
        $retentionDate = (new DateTime())->sub(new \DateInterval('P1M'))->format('Y-m-d'); // Subtract 1 month from the current date

        // Implement any necessary preprocessing steps here
        return array_filter(
            // project $.hits[*].event
            array_map(fn($item) => $item['event'] ?? [], $data['hits'] ?? []),
            // filter out events that have any of the exclude tags or have an end date before the retention date
            fn($item) =>
            (count(
                array_intersect(
                    array_map('strtolower', $item['tags'] ?? []),
                    $this->excludeTags
                )
            ) === 0) && ($item['endDate'] ?? '') >= $retentionDate
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
            new MapOffers(), // Do not include products in offers
            new MapEventStatus(),
            new MapKeywords(),
            new MapPhysicalAccessibilityFeatures(),
            new MapPotentialAction(),
            new MapUrl($this->externalBaseUrl),
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
