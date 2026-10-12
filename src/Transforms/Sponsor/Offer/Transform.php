<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer;

use Municipio\Schema\Schema;
use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapImage;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapLocation;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\Mappers\MapOfferedBy;

class Transform implements AbstractDataTransform
{
    public function preprocessData(array $data): array
    {
        return $data;
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapOffer(),
            new MapImage(),
            new MapLocation(),
            new MapKeywords(),
            new MapOfferedBy(),
        ];
        return array_map(fn ($item) =>
            array_reduce(
                $mappers,
                fn ($project, $mapper) => $mapper->map($project, $item),
                Schema::SponsorOffer()
            )->toArray(), array_values($data));
    }
}
