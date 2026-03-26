<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPHeadLessEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapTypicalAgeRange;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapUrl;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapMapXCreatedBy;
use SchemaTransformer\Transforms\Event\WPHeadLessEvents\Mappers\MapPotentialAction;

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
            new MapEventSchedule(),
            new MapOffers(),
            new MapEventStatus(),
            new MapKeywords(),
            new MapPhysicalAccessibilityFeatures(),
            new MapTypicalAgeRange(),
            new MapUrl(),
            new MapPotentialAction(),
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
