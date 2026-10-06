<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapLocation extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\Place
    {
        return $this->withAcfFields(
            $data,
            fn(array $acf) =>
                Schema::Place()
                    ->address(Schema::PostalAddress()
                    ->streetAddress($acf['name'] ?? null)
                    ->addressLocality($acf['city'] ?? null)
                    ->addressRegion($acf['state'] ?? null)
                    ->postalCode($acf['post_code'] ?? null)
                    ->addressCountry($acf['country'] ?? null))->description($acf['address'] ?? null),
            'location'
        );
    }
}
