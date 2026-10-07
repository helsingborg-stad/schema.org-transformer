<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\Sponsor\Demand\Mappers;

use Municipio\Schema\Schema;
use Municipio\Schema\SponsorDemandEvent;
use SchemaTransformer\Transforms\Sponsor\Demand\AbstractMapper;

class MapSponsorshipOffer extends AbstractMapper
{
    private const RESOURCE_KEY = 'resources';

    protected function mapCategories(array $data): array
    {
        return $this->withAcfFields(
            $data,
            fn (array $acf) =>
            array_map(fn (array $row) => $row['name'] ?? null, $acf),
            self::RESOURCE_KEY
        ) ?? [];
    }

    public function map(SponsorDemandEvent $event, array $data): SponsorDemandEvent
    {
        return $event->hasSponsorshipOffer(
            $this->withAcfFields(
                $data,
                fn(array $acf) =>
                    Schema::sponsorOffer()
                        ->availabilityEnds(($date = $this->mapDateTime($acf['due_date'] ?? null, $acf['due_time'] ?? null)) ? $date->format('Y-m-d\TH:i:s') : '')
                        ->category($this->mapCategories($data))
                        ->demand(
                            Schema::Demand()->description($acf['requirements'] ?? null)
                        ),
                null
            )
        ) ?? $event;
    }
}
