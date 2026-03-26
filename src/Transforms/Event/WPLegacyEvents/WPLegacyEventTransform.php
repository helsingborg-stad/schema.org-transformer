<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\WPLegacyEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapMapXCreatedBy;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapTypicalAgeRange;
use SchemaTransformer\Transforms\Event\WPLegacyEvents\Mappers\MapUrl;

class WPLegacyEventTransform extends TransformBase implements AbstractDataTransform
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
