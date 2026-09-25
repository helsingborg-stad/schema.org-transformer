<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\Offer;

class SponsorOffer extends Offer
{
    public function toArray(): array
    {
        $data          = parent::toArray();
        $data['@type'] = 'municipio:SponsorOffer';

        return $data;
    }
}
