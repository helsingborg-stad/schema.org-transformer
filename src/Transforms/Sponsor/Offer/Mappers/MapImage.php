<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;

class MapImage extends AbstractMapper
{
    private const IMAGE_KEY = 'image';

    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        return $offer->image(
            $this->withAcfFields(
                $data,
                fn(array $acf) => Schema::imageObject()
                ->url($acf['url'] ?? null)
                ->name($acf['title'] ?? null)
                ->description($acf['alt'] ?? null),
                self::IMAGE_KEY
            )
        );
    }
}
