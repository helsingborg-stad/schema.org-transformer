<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\TixEvents;

use Municipio\Schema\Schema;
use Override;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\TixEvents\Mappers\MapXCreatedBy;

class TixEventTransform extends TransformBase implements AbstractDataTransform
{
    private array $filters = [];
    public function __construct(string $idprefix, array $filters = [])
    {
        parent::__construct($idprefix);
        $this->filters = $filters;
    }

    #[Override]
    public function preprocessData(array $data): array
    {
        if (empty($this->filters)) {
            return parent::preprocessData($data);
        }

        return array_values(array_filter(
            parent::preprocessData($data),
            function ($item) {
                foreach ($this->filters as $filter) {
                    if (!$filter($item)) {
                        return false;
                    }
                }
                return true;
            }
        ));
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
            new MapEventSchedule($this),
            new MapOffers(false), // Do not include products in offers
            new MapEventStatus(),
            new MapKeywords(),
            new MapPhysicalAccessibilityFeatures(),
            new MapXCreatedBy()

        ];
        $result = array_map(function ($item) use ($mappers) {
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
