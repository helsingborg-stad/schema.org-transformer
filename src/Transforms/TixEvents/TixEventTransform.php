<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\TixEvents;

use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\TransformBase;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapDescription;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapEndDate;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapEventSchedule;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapIsAccessibleForFree;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapName;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapStartDate;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapIdentifier;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapImage;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapLocation;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapOffers;
use SchemaTransformer\Transforms\TixEvents\Mappers\MapOrganizer;

class TixEventTransform extends TransformBase implements AbstractDataTransform
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
            new MapStartDate(),
            new MapEndDate(),
            new MapOrganizer(),
            new MapLocation(),
            new MapImage(),
            new MapEventSchedule($this),
            new MapOffers(),
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
