<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapOrganization extends AbstractMapper
{
    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        return $event->organization(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                 Schema::Organization()
                    ->name($acf['organization_name'] ?? null)
                    ->description($acf['organization_description'] ?? null)
                    ->url($acf['organization_website'] ?? $acf['organization_url'] ?? null)
                    ->taxID($acf['organization_number'] ?? null)
                    ->contactPoint(Schema::ContactPoint()
                        ->name($acf['organization_contact'] ?? null)
                        ->role($acf['organization_contact_role'] ?? null)
                        ->email($acf['organization_email'] ?? null)
                        ->telephone($acf['organization_phone'] ?? null),),
                null
            )
        ) ?? $event;
    }
}
