<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\WPLegacyEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapName;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapTypicalAgeRange;
use SchemaTransformer\Transforms\WPLegacyEvents\Mappers\MapUrl;

class WPLegacyEventTransform2 extends TransformBase implements AbstractDataTransform
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
            new MapUrl()
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
