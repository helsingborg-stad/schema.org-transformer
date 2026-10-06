<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapDemand extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\Demand
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            Schema::Demand()
                ->description($acf['requirements'] ?? null),
            null
        );
    }
}
