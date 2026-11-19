<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Offer\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorOffer;
use SchemaTransformer\Transforms\Sponsor\Offer\AbstractMapper;

class MapOfferedBy extends AbstractMapper
{
    public function map(SponsorOffer $listing, array $data): SponsorOffer
    {
        $acf = $data['acf'] ?? [];
        $id = $listing->getProperty('@id');
        if (!$acf || !$id) {
            return $listing;
        }

        $organization = Schema::organization()
            ->setProperty('@id', $id . '#organization')
            ->name($acf['organization_name'] ?? null)
            ->description($acf['organization_description'] ?? null)
            ->url($acf['organization_website'] ?? null)
            ->contactPoint(
                Schema::contactPoint()
                    ->name($acf['organization_contact'] ?? null)
                    ->description($acf['organization_contact_role'] ?? null)
                    ->email($acf['organization_email'] ?? null)
                    ->telephone((string) ($acf['organization_phone'] ?? ''))
            );

        return $listing->offeredBy($organization);
    }
}
