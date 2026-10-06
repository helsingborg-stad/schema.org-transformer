<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapOffer extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\SponsorOffer
    {
        return $this->withAcfFields(
            $data,
            fn(array $acf) =>
                Schema::sponsorOffer()
                    ->availabilityEnds(($date = $this->mapDateTime($acf['due_date'], $acf['due_time'])) ? $date->format('Y-m-d\TH:i:s') : '')
                    ->category(new MapCategories()->map($data)),
            null
        );
    }
}
