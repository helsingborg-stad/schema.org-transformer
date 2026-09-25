<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms;

use Municipio\Schema\Event;

class SponsorDemand extends Event
{
    public function toArray(): array
    {
        $data          = parent::toArray();
        $data['@type'] = 'municipio:SponsorDemandEvent';

        return $data;
    }
}
