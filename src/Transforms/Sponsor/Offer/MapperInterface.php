<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer;

use Municipio\Schema\SponsorOffer;

interface MapperInterface
{
    public function map(SponsorOffer $offer, array $data): SponsorOffer;
}
