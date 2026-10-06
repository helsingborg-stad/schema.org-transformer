<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Mappers;

use Municipio\Schema\Schema;

class MapContactPoint extends AbstractSponsorMapper
{
    public function map(array $data): ?\Municipio\Schema\ContactPoint
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            Schema::ContactPoint()
            ->name($acf['organization_contact'] ?? null)
            ->role($acf['organization_contact_role'] ?? null)
            ->email($acf['organization_email'] ?? null)
            ->telephone($acf['organization_phone'] ?? null),
            null
        );
    }
}
