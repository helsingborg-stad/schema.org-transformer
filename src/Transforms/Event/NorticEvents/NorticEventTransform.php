<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Event\NorticEvents;

use Municipio\Schema\Schema;
use Override;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\NorticEventDataMapperInterface;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventAttendanceMode;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapEventStatus;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapName;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapOrganizer;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapPhysicalAccessibilityFeatures;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapPotentialAction;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapTypicalAgeRange;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapUrl;
use SchemaTransformer\Transforms\Event\NorticEvents\Mappers\MapXCreatedBy;
use SchemaTransformer\Transforms\TransformBase;

class NorticEventTransform extends TransformBase implements AbstractDataTransform
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
        // unwrap events from the envelope
        $d = $data['events'] ?? [];

        // run filters if any are defined
        if (empty($this->filters)) {
            return parent::preprocessData($d);
        }

        return array_values(array_filter(
            parent::preprocessData($d),
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
        /** @var NorticEventDataMapperInterface[] $mappers */
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
            new MapTypicalAgeRange(),
            new MapUrl(),
            new MapXCreatedBy(),
        ];

        $events = array_is_list($data) ? $data : [$data];

        return array_map(
            fn (array $item) => array_reduce(
                $mappers,
                fn ($event, $mapper) => $mapper->map($event, $item),
                Schema::event()
            )->toArray(),
            $events
        );
    }
}
