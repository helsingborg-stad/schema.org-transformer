<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapDemandEvent extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\SponsorDemandEvent
    {
        return $this->withAcfFields(
            $data,
            fn(array $acf) =>
                 Schema::sponsorDemandEvent()
                ->identifier($data['id'] ?? null)
                ->name($data['title']['rendered'] ?? '')
                ->description($acf['description'] ?? null)
                ->startDate(($date = $this->mapDateTime($acf['date'], $acf['time'])) ? $date->format('Y-m-d\TH:i:s') : null),
            null
        );
    }
}
