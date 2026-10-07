<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;

class MapOfferedBy extends AbstractMapper
{
    public function map(SponsorOffer $offer, array $data): SponsorOffer
    {
        return $offer->offeredBy(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                    Schema::Organization()
                        ->name($acf['organization_name'] ?? null)
                        ->description($acf['organization_description'] ?? null)
                        ->url($acf['organization_website'] ?? $acf['organization_url'] ?? null)
                        ->taxID($acf['organization_number'] ?? null)
                        ->contactPoint(
                            Schema::ContactPoint()
                                ->name($acf['organization_contact'] ?? null)
                                ->role($acf['organization_contact_role'] ?? null)
                                ->email($acf['organization_email'] ?? null)
                                ->telephone($acf['organization_phone'] ?? null)
                        )
                        ->demand(Schema::Demand()->description($acf['requirements'] ?? null))
                        ->keywords([Schema::definedTerm()
                            ->name($acf['proposal_for_counter_performance'] ?? null)
                            ->inDefinedTermSet(Schema::definedTermSet()->name('proposal_for_counter_performance'))]),
                null
            )
        );
    }
}
