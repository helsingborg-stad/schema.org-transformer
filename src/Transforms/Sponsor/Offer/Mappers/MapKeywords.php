<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;
use SchemaTransformer\Transforms\Sponsor\MapTerms;

class MapKeywords extends AbstractMapper
{
    public function map(SponsorOffer $listing, array $data): SponsorOffer
    {
        return $listing->keywords(MapTerms::fromRows($data['acf']['resources'] ?? [], 'resource'));
    }
}
