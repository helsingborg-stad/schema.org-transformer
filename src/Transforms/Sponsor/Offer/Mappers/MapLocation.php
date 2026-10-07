<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;

class MapLocation extends AbstractMapper
{
    private const LOCATION_KEY = 'location';

    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        return
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                    $offer->location(Schema::Place()
                        ->address(Schema::PostalAddress()
                        ->streetAddress($acf['name'] ?? null)
                        ->addressLocality($acf['city'] ?? null)
                        ->addressRegion($acf['state'] ?? null)
                        ->postalCode($acf['post_code'] ?? null)
                        ->addressCountry($acf['country'] ?? null))->description($acf['address'] ?? null)),
                self::LOCATION_KEY
            ) ?? $offer;
    }
}
