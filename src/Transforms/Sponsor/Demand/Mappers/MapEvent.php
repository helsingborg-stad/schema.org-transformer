<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapEvent extends AbstractMapper
{
    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        $acf  = is_array($data['acf'] ?? null) ? $data['acf'] : [];
        $date = $this->mapDateTime($acf['date'] ?? null, $acf['time'] ?? null);

        return $event
            ->identifier($data['id'] ?? null)
            ->name($data['title']['rendered'] ?? '')
            ->description($acf['description'] ?? null)
            ->startDate($date?->format('Y-m-d\TH:i:s'));
    }
}
