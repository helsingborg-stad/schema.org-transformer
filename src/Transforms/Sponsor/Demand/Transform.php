<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand;

use SchemaTransformer\Interfaces\AbstractDataTransform;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapDemand;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapImage;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapKeywords;
use SchemaTransformer\Transforms\Sponsor\Demand\Mappers\MapOrganization;
use Municipio\Schema\Schema;
use SchemaTransformer\Transforms\Sponsor\MapWebPage;

class Transform implements AbstractDataTransform
{
    public function preprocessData(array $data): array
    {
        return $data;
    }

    public function transform(array $data): array
    {
        $mappers = [
            new MapDemand(),
            new MapImage(),
            new MapOrganization(),
            new MapKeywords(),
            new MapWebPage(),
        ];
        return array_map(fn ($item) =>
            array_reduce(
                $mappers,
                fn ($project, $mapper) => $mapper->map($project, $item),
                Schema::SponsorDemand()
            )->toArray(), array_values($data));
    }
}
