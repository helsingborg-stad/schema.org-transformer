<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapOrganization extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\Organization
    {
        return $this->withAcfFields(
            $data,
            fn(array $acf) =>
             Schema::Organization()
            ->name($acf['organization_name'] ?? null)
            ->description($acf['organization_description'] ?? null)
            ->url($acf['organization_url'] ?? null)
            ->taxID($acf['organization_number'] ?? null),
            null
        );
    }
}
