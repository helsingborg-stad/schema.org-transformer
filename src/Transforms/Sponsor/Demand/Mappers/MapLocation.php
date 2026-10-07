<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapLocation extends AbstractMapper
{
    private const LOCATION_KEY = 'location';

    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        return $event->location(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                    Schema::Place()
                        ->address(Schema::PostalAddress()
                        ->streetAddress($acf['name'] ?? null)
                        ->addressLocality($acf['city'] ?? null)
                        ->addressRegion($acf['state'] ?? null)
                        ->postalCode($acf['post_code'] ?? null)
                        ->addressCountry($acf['country'] ?? null))->description($acf['address'] ?? null),
                self::LOCATION_KEY
            )
        ) ?? $event;
    }
}
