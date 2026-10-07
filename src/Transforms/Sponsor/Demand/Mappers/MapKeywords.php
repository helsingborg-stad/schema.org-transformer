<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapKeywords extends AbstractMapper
{
    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        $keywords = $this->withAcfFields(
            $data,
            static fn (array $acf): array => ($acf['organization_eligible_for_grants'] ?? false) === true
                ? [Schema::definedTerm()
                    ->name('eligible')
                    ->inDefinedTermSet(Schema::definedTermSet()->name('organization_eligible_for_grants'))]
                : [],
            null
        );

        return $keywords === null ? $event : $event->keywords($keywords);
    }
}
