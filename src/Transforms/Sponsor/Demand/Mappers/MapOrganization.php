<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemand;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;
use SchemaTransformer\Transforms\Sponsor\MapTerms;

class MapOrganization extends AbstractMapper
{
    public function map(SponsorDemand $listing, array $data): SponsorDemand
    {
        $acf = $data['acf'] ?? [];
        $id  = $listing->getProperty('@id');
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

        $organization->keywords(MapTerms::fromRows($acf['activities'] ?? [], 'activity'));

        return $listing->setProperty('@reverse', ['seeks' => $organization]);
    }
}
