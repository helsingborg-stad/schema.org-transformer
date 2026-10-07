<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand;

use Municipio\Schema\SponsorDemandEvent;

interface MapperInterface
{
    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent;
}
