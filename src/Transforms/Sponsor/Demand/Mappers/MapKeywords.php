<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\SponsorDemand;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;
use SchemaTransformer\Transforms\Sponsor\MapTerms;

class MapKeywords extends AbstractMapper
{
    public function map(SponsorDemand $listing, array $data): SponsorDemand
    {
        return $listing->keywords(MapTerms::fromRows($data['acf']['resources'] ?? [], 'resource'));
    }
}
