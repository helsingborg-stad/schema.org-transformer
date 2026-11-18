<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\MapWebPage;

class MapOffer extends AbstractMapper
{
    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        $acf  = is_array($data['acf'] ?? null) ? $data['acf'] : [];
        $date = $this->mapDateTime($acf['date'] ?? null, $acf['time'] ?? null);

        return $offer
            ->setProperty('@id', MapWebPage::listingId($data))
            ->name($data['title']['rendered'] ?? '')
            ->description($acf['description'] ?? null)
            ->sponsorshipDate($date)
            ->practicalInformation($acf['location']['address'] ?? null)
            ->requestedCounterPerformance($acf['proposal_for_counter_performance'] ?? null);
    }
}
