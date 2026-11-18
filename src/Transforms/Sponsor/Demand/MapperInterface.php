<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand;

use Municipio\Schema\SponsorDemand;

interface MapperInterface
{
    public function map(SponsorDemand $event, array $data): SponsorDemand;
}
