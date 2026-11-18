<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\SponsorDemand;
use SchemaTransformer\Transforms\Sponsor\MapWebPage;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapDemand extends AbstractMapper
{
    public function map(SponsorDemand $event, array $data): SponsorDemand
    {
        $acf  = is_array($data['acf'] ?? null) ? $data['acf'] : [];
        $date = $this->mapDateTime($acf['date'] ?? null, $acf['time'] ?? null);

        return $event
            ->setProperty('@id', MapWebPage::listingId($data))
            ->name($data['title']['rendered'] ?? '')
            ->description($acf['description'] ?? null)
            ->supportNeededBy($date)
            ->supportLocation($acf['location']['address'] ?? null)
            ->sponsorPreferences($acf['requirements'] ?? null)
            ->counterPerformance($acf['proposal_for_counter_performance'] ?? null);
    }
}
