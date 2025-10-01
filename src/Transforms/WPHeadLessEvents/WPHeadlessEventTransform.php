<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPHeadLessEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapName;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\WPHeadLessEvents\Mappers\MapMapXCreatedBy;

class WPHeadlessEventTransform extends TransformBase implements AbstractDataTransform
{
    public function __construct(string $idprefix)
    {
        parent::__construct($idprefix);
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
            // new MapEventSchedule(),
            // new MapOffers(),
            // new MapEventStatus(),
            // new MapKeywords(),
            // new MapPhysicalAccessibilityFeatures(),
            // new MapTypicalAgeRange(),
            // new MapUrl(),
            new MapMapXCreatedBy()
        ];

        $result = array_map(function ($item) use ($mappers) {
            return array_reduce(
                $mappers,
                function ($event, $mapper) use ($item) {
                    return $mapper->map($event, $item);
                },
                Schema::event()
            )->toArray();
        }, array_values($data));
        return $result;
    }
}
