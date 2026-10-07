<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapImage;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapLocation;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapOrganization;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapSponsorshipOffer;
use Municipio\Schema\Schema;

class Transform implements AbstractDataTransform
{
    public function preprocessData(array $data): array
    {
        return $data;
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapEvent(),
            new MapImage(),
            new MapLocation(),
            new MapSponsorshipOffer(),
            new MapOrganization(),
            new MapKeywords(),
        ];
        return array_map(fn ($item) =>
            array_reduce(
                $mappers,
                fn ($project, $mapper) => $mapper->map($project, $item),
                Schema::SponsorDemandEvent()
            )->toArray(), array_values($data));
    }
}
